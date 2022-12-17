<?php
namespace teller;

use App\Model\AccountModel;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Core;

use kornrunner\Keccak;
use function Swoole\Coroutine\run;

defined('SWOOLE_VERSION') or define('SWOOLE_VERSION', intval(phpversion('swoole')));
defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT', realpath(getcwd()));
defined('EASYSWOOLE_SERVER') or define('EASYSWOOLE_SERVER', 1);
defined('EASYSWOOLE_WEB_SERVER') or define('EASYSWOOLE_WEB_SERVER', 2);
defined('EASYSWOOLE_WEB_SOCKET_SERVER') or define('EASYSWOOLE_WEB_SOCKET_SERVER', 3);

defined('IN_PHAR') or define('IN_PHAR', boolval(\Phar::running(false)));
defined('RUNNING_ROOT') or define('RUNNING_ROOT', realpath(getcwd()));
defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT', IN_PHAR ? \Phar::running() : realpath(getcwd()));

$file = EASYSWOOLE_ROOT.'/vendor/autoload.php';
require $file;



$configfile = EASYSWOOLE_ROOT . '/dev.php';
Config::getInstance()->loadFile($configfile);
require_once EASYSWOOLE_ROOT.'/bootstrap.php';

Core::getInstance()->initialize();

run(function() {

    $r = config('simulate_host');

    var_dump($r);
    $data = [
        'deletion'=>['token'=>'xxx','expire'=>ts()]
    ];
//    AccountModel::getInstance()->insertOne($data);
//    $r = AccountModel::getInstance()->gets();
//    var_dump($r);
    $filter = ['deletion.token'=>'x2xx'];
    $r = AccountModel::getInstance()->gets($filter);
    var_dump($r);
});