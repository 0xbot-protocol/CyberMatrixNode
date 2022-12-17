<?php


namespace EasySwoole\EasySwoole;


use App\Crontab\TaskNPower;
use App\Model\Quant\BotSimulatePriceModel;
use App\Quant\Manager\SessionManager;
use App\Utility\PoolTools;
use App\WebSocket\WebSocketEvent;
use App\WebSocket\WebSocketParser;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Crontab\Crontab;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\FileWatcher\FileWatcher;
use EasySwoole\FileWatcher\WatchRule;
use EasySwoole\Socket\Dispatcher;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {

        #热加载
        $watcher = new FileWatcher();
        $rule = new WatchRule(EASYSWOOLE_ROOT . "/App"); // 设置监控规则和监控目录
        $watcher->addRule($rule);
        $watcher->setOnChange(function () {
            Logger::getInstance()->info('file change ,reload!!!');
            ServerManager::getInstance()->getSwooleServer()->reload();
        });
        $watcher->attachServer(ServerManager::getInstance()->getSwooleServer());






        #新的Pool
        PoolTools::initPool();

        #websocket配置
        /**
         * **************** websocket控制器 **********************
         */
        //创建一个 Dispatcher 配置
        $conf = new \EasySwoole\Socket\Config();

        //设置Dispatcher为WebSocket 模式
        $conf->setType(\EasySwoole\Socket\Config::WEB_SOCKET);

        //设置解析器对象
        $conf->setParser(new WebSocketParser());

        //创建Dispatcher对象并注入config对象
        $dispatch = new Dispatcher($conf);

        //给server注册相关事件在WebSocket模式下onMessage事件必须注册 并且交给Dispatcher对象处理
        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {

            $dispatch->dispatch($server, $frame->data, $frame);

        });

        //自定义握手事件
        $websocketEvent = new WebSocketEvent();

        $register->set(EventRegister::onHandShake, function (\swoole_http_request $request, \swoole_http_response $response) use ($websocketEvent) {
            $websocketEvent->onHandShake($request, $response);
        });

        //自定义关闭事件
        $register->set(EventRegister::onClose, function (\swoole_server $server, int $fd, int $reactorId) use ($websocketEvent) {
            $websocketEvent->onClose($server, $fd, $reactorId);
        });




        $register->add(EventRegister::onWorkerStart, function (\swoole_server $server, $workerId) {
            //如何避免定时器因为进程重启而丢失
            //例如在第一个进程 添加一个10秒的定时器
            if ($workerId == 0) {

                \EasySwoole\Component\Timer::getInstance()->loop(1 * 1000, function(){



                });
            }
        });

    }
}