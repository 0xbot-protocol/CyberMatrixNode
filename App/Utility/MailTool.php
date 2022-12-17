<?php


namespace App\Utility;


/**
 * email smtp （support php7）
 *
 */
class Smtp
{
    /* Public Variables */
    public $smtp_port;
    public $time_out;
    public $host_name;
    public $log_file;
    public $relay_host;
    public $debug;
    public $auth;
    public $user;
    public $pass;
    /* Private Variables */
    private $sock;

    /* Constractor */
    function __construct($relay_host = "", $smtp_port = 25, $auth = false, $user, $pass)
    {
        $this->debug = true;
        $this->smtp_port = $smtp_port;
        $this->relay_host = $relay_host;
        $this->time_out = 30; //is used in fsockopen()
        $this->auth = $auth;//auth
        $this->user = $user;
        $this->pass = $pass;
        $this->host_name = "localhost"; //is used in HELO command
        $this->log_file = "mail.log";
        $this->sock = FALSE;
    }

    /* Main Function */
    function sendmail($to, $from, $subject, $body, $mailtype, $cc, $bcc, $additional_headers, $fromUser, $replyToAddress)
    {
        $mail_from = $this->get_address($this->strip_comment($from));
        $body = preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $body);
        $header = "MIME-Version:1.0\r\n";
        if ($mailtype == "HTML") {
            $header .= "Content-Type:text/html; charset=utf-8\r\n";
        }
        $header .= "To: " . $to . "\r\n";
        if ($cc != "") {
            $header .= "Cc: " . $cc . "\r\n";
        }
        $header .= "From: $fromUser<" . $from . ">\r\n";
        $header .= "Subject: " . $subject . "\r\n";
        $header .= "Reply-To: " . $replyToAddress . "\r\n";
        $header .= $additional_headers;
        $header .= "Date: " . date("r") . "\r\n";
        $header .= "X-Mailer:By Redhat (PHP/" . phpversion() . ")\r\n";
        list($msec, $sec) = explode(" ", microtime());
        $header .= "Message-ID: <" . date("YmdHis", $sec) . "." . ($msec * 1000000) . "." . $mail_from . ">\r\n";
        $TO = explode(",", $this->strip_comment($to));
        if ($cc != "") {
            $TO = array_merge($TO, explode(",", $this->strip_comment($cc)));
        }
        if ($bcc != "") {
            $TO = array_merge($TO, explode(",", $this->strip_comment($bcc)));
        }
        $sent = TRUE;
        foreach ($TO as $rcpt_to) {
            $rcpt_to = $this->get_address($rcpt_to);
            if (!$this->smtp_sockopen($rcpt_to)) {
                track_error("Error: Cannot send email to " . $rcpt_to . "\n");
                $sent = FALSE;
                continue;
            }
            if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $body)) {
                track_info("E-mail has been sent to <" . $rcpt_to . ">\n");
            } else {
                track_error("Error: Cannot send email to <" . $rcpt_to . ">\n");
                $sent = FALSE;
            }
            fclose($this->sock);
            track_info("Disconnected from remote host\n");
        }
        return $sent;
    }

    /* Private Functions */
    function smtp_send($helo, $from, $to, $header, $body = "")
    {
        if (!$this->smtp_putcmd("HELO", $helo)) {
            return $this->smtp_error("sending HELO command");
        }
        //auth
        if ($this->auth) {
            if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user))) {
                return $this->smtp_error("sending HELO command");
            }
            if (!$this->smtp_putcmd("", base64_encode($this->pass))) {
                return $this->smtp_error("sending HELO command");
            }
        }
        if (!$this->smtp_putcmd("MAIL", "FROM:<" . $from . ">")) {
            return $this->smtp_error("sending MAIL FROM command");
        }
        if (!$this->smtp_putcmd("RCPT", "TO:<" . $to . ">")) {
            return $this->smtp_error("sending RCPT TO command");
        }
        if (!$this->smtp_putcmd("DATA")) {
            return $this->smtp_error("sending DATA command");
        }
        if (!$this->smtp_message($header, $body)) {
            return $this->smtp_error("sending message");
        }
        if (!$this->smtp_eom()) {
            return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
        }
        if (!$this->smtp_putcmd("QUIT")) {
            return $this->smtp_error("sending QUIT command");
        }
        return TRUE;
    }

    function smtp_sockopen($address)
    {
        if ($this->relay_host == "") {
            return $this->smtp_sockopen_mx($address);
        } else {
            return $this->smtp_sockopen_relay();
        }
    }

    function smtp_sockopen_relay()
    {
       track_info("Trying to " . $this->relay_host . ":" . $this->smtp_port . "\n");
        $this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
        if (!($this->sock && $this->smtp_ok())) {
           track_info("Error: Cannot connenct to relay host " . $this->relay_host . "\n");
           track_info("Error: " . $errstr . " (" . $errno . ")\n");
            return FALSE;
        }
       track_info("Connected to relay host " . $this->relay_host . "\n");
        return TRUE;
    }

    function smtp_sockopen_mx($address)
    {
        $domain = preg_replace("/^.+@([^@]+)$/", "\1", $address);
        if (!@getmxrr($domain, $MXHOSTS)) {
           track_info("Error: Cannot resolve MX \"" . $domain . "\"\n");
            return FALSE;
        }
        foreach ($MXHOSTS as $host) {
           track_info("Trying to " . $host . ":" . $this->smtp_port . "\n");
            $this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
            if (!($this->sock && $this->smtp_ok())) {
               track_info("Warning: Cannot connect to mx host " . $host . "\n");
               track_info("Error: " . $errstr . " (" . $errno . ")\n");
                continue;
            }
           track_info("Connected to mx host " . $host . "\n");
            return TRUE;
        }
       track_info("Error: Cannot connect to any mx hosts (" . implode(", ", $MXHOSTS) . ")\n");
        return FALSE;
    }

    function smtp_message($header, $body)
    {
        fputs($this->sock, $header . "\r\n" . $body);
        $this->smtp_debug("> " . str_replace("\r\n", "\n" . "> ", $header . "\n> " . $body . "\n> "));
        return TRUE;
    }

    function smtp_eom()
    {
        fputs($this->sock, "\r\n.\r\n");
        $this->smtp_debug(". [EOM]\n");
        return $this->smtp_ok();
    }

    function smtp_ok()
    {
        $response = str_replace("\r\n", "", fgets($this->sock, 512));
        $this->smtp_debug($response . "\n");
        if (!preg_match("/^[23]/", $response)) {
            fputs($this->sock, "QUIT\r\n");
            fgets($this->sock, 512);
           track_info("Error: Remote host returned \"" . $response . "\"\n");
            return FALSE;
        }
        return TRUE;
    }

    function smtp_putcmd($cmd, $arg = "")
    {
        if ($arg != "") {
            if ($cmd == "") $cmd = $arg;
            else $cmd = $cmd . " " . $arg;
        }
        fputs($this->sock, $cmd . "\r\n");
        $this->smtp_debug("> " . $cmd . "\n");
        return $this->smtp_ok();
    }

    function smtp_error($string)
    {
       track_info("Error: Error occurred while " . $string . ".\n");
        return FALSE;
    }



    function strip_comment($address)
    {
        $comment = "/\([^()]*\)/";
        while (preg_match($comment, $address)) {
            $address = preg_replace($comment, "", $address);
        }
        return $address;
    }

    function get_address($address)
    {
        $address = preg_replace("/([ \t\r\n])+/", "", $address);
        $address = preg_replace("/^.*<(.+)>.*$/", "\1", $address);
        return $address;
    }

    function smtp_debug($message)
    {
        var_dump($message);
        if ($this->debug) {
            echo $message;
        }
    }
}

class MailTool
{
    public static  function sendMail($mailto,$mailsubject,$mailbody,$sender) {


        track_info("$mailto,$mailsubject,$mailbody,$sender");


        $smtpserver     = "smtpdm.aliyun.com";
        $smtpserverport = 80;
        $smtpusermail   = "service@mail.yqkkn.com";
// 发件人的账号，填写控制台配置的发信地址,比如xxx@xxx.com
        $smtpuser       = "service@mail.yqkkn.com";
// 访问SMTP服务时需要提供的密码(在控制台选择发信地址进行设置)
        $smtppass       = "A0Eiduoduo20182";
        $mailsubject    = "=?UTF-8?B?" . base64_encode($mailsubject) . "?=";
        $mailtype       = "HTML";
//可选，设置回信地址
        $smtpreplyto    = "service@mail.yqkkn.com";
        $smtp           = new Smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass);
        $smtp->debug    = false;
        $cc   ="";
        $bcc  = "";
        $additional_headers = "";
//设置发件人名称，名称用户可以自定义填写。
        $flag = $smtp->sendmail($mailto,$smtpusermail, $mailsubject, $mailbody, $mailtype, $cc, $bcc, $additional_headers, $sender, $smtpreplyto);
        return $flag;
    }

}


