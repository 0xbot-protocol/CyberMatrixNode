<?php

namespace App\Mongo;

use EasySwoole\EasySwoole\Trigger;
use EasySwoole\SyncInvoker\AbstractDriver;
use MongoDB\Client;


class MongoDriver  extends AbstractDriver
{

    protected $db;
    protected $client;

    public function __construct(MongoConfig $config=null)
    {
        if($this->client) {
            return $this->client;
        }
        if($this->config == null) {

            $conf = config('MongoDB');
            $config = new MongoConfig($conf);
        }
        $this->config = $config->toArray();
//        var_dump($this->config);


        $driverOptions=[
            'typeMap' => [
                'array' => 'array',
                'document' => 'array',
                'root' => 'array',
            ]
        ];
        $username = $this->config['username'] ?? '';
        $password = $this->config['password'] ?? '';
        $authSource = $this->config['authSource'] ?? '';
        $replicaSet = $this->config['rs'] ?? '';
        if (empty($uriOptions['username'])) {
            $uriOptions['username'] = $username;
        }
        if (empty($uriOptions['password'])) {
            $uriOptions['password'] = $password;
        }
        if (empty($uriOptions['replicaSet']) && !empty($replicaSet)) {
            $uriOptions['replicaSet'] = $replicaSet;
        }
        if (empty($uriOptions['authSource']) && !empty($authSource)) {
            $uriOptions['authSource'] = $authSource;
        }
//        track_error(json_encode($uriOptions));


//        var_dump($config);


        if(empty($uriOptions['password'])) {
            $this->client = new \MongoDB\Client(
                $config->getHost(),
                [],
                $driverOptions
            );
        } else {
//            $this->client = new \MongoDB\Client(
//                $config->getHost(),
//                $uriOptions,
//                $driverOptions
//            );

            $this->client = new \MongoDB\Client(
                $config->getHost(),
                $uriOptions,
                $driverOptions
            );
        }
//        track_error($config->getHost());


        $this->dbName = $config->getDb();
        return $this->client;

    }

    //unset 的时候执行
    public function gc() {
//        track_info(__METHOD__);

    }
    //使用后,free的时候会执行
    function objectRestore()
    {
//        $cid = Coroutine::getCid();
//        track_info(__METHOD__."|{$cid}");

    }
    //使用前调用,当返回true，表示该对象可用。返回false，该对象失效，需要回收
    function beforeUse():?bool {
//        track_info(__METHOD__);
        return true;
    }

    public function getDB($dbName="")
    {
        if($dbName == "") {
            return $this->getClient()->{$this->dbName};
        } else {
            return $this->getClient()->{$dbName};
        }
        //$this->db;
    }

    public function getClient() {
        return $this->client;
    }

    public function getCol($table)
    {
        return $this->getDB()->{$table};
    }




}
