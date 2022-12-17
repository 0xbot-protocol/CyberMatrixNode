<?php
/**
 * Created by PhpStorm.
 * Index: root
 * Date: 18-11-1
 * Time: 下午1:49
 */

namespace App\Model;


use App\Bean\EvtBean;
use App\Model\System\EventModel;
use App\Model\System\OperationLogModel;
use App\Mongo\MongoClient;
use App\Mongo\MongoConfig;
use App\Mongo\MongoDriver;
use App\Pool\MongoDB;
use App\Utility\CacheTools;


use EasySwoole\EasySwoole\Task\TaskManager;



class BaseModel
{


    private $config;
//    protected $table;
    protected  $client;
    protected  $db;
    protected  $sync=1;  //先不使用异步





    public function __construct($app="",$key='MongoDB') {
        $conf = config($key);
        $config = new MongoConfig($conf);
        $this->config = $config->toArray();

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
        $uriOptions['readPreference'] = "primarypreferred";


        if(empty($uriOptions['password'])) {
            $this->client = new \MongoDB\Client(
                $config->getHost(),
                [],
                $driverOptions
            );
        } else {

            $this->client = new \MongoDB\Client(
                $config->getHost(),
                $uriOptions,
                $driverOptions
            );
        }


        $this->db = $this->client->{$config->getDb()};


    }


    /**
     * 返回标准格式数据
     * @param $code
     * @param string $msg
     * @param array $result
     * @return array
     */
    function setRet($code, $msg="", $result=[]) {
        return ['code'=>$code,'msg'=>$msg,'result'=>$result];
    }


    public function page($filter,$options) {
        $data = $this->getCol(static::$table)->find($filter,$options);

        $total = $this->getCol(static::$table)->countDocuments($filter);

        if(!empty($options['skip'])) {
            if(!empty($options['limit'])) {
                $num = intval($options['skip']/$options['limit']+1);
            } else {
                $num = intval($options['skip']);
            }


        } else {
            $num = 1;
        }


        if(isset($options['limit']) && $options['limit']>0) {
            $totalPage = ceil($total/$options['limit']);

        } else {
            $pageSize =  $pageSize = config('pagesize');
            $totalPage = ceil($total/$pageSize);
        }



        return ['code'=>0,'result'=>['total'=>$total,'page'=>$num,'totalPage'=>$totalPage,'list'=>iterator_to_array($data)],'msg'=>''];    }



    public function getPage($app,$filter=[],$options=[]) {
        $data = $this->tbl($app)->find($filter,$options);
        $total = $this->tbl($app)->countDocuments($filter);

        if(!empty($options['skip'])) {
            if(!empty($options['limit'])) {
                $num = intval($options['skip']/$options['limit']+1);
            } else {
                $num = intval($options['skip']);
            }


        } else {
            $num = 1;
        }
        if(isset($options['limit']) && $options['limit']>0) {
            $totalPage = ceil($total/$options['limit']);

        } else {
            $pageSize =  $pageSize = config('pagesize');
            $totalPage = ceil($total/$pageSize);
        }



        return ['code'=>0,'result'=>['total'=>$total,'page'=>$num,'totalPage'=>$totalPage,'list'=>iterator_to_array($data)],'msg'=>''];

    }

    public function pagelist($filter=[],$skip=0,$limit=20) {
        $data = $this->getCol(static::$table)->find($filter,[
            'limit'=>$limit,
            'skip'=>$skip
        ]);
        return ['code'=>0,'result'=>iterator_to_array($data),'msg'=>''];

    }



    /**
     * 获取一个事务ID
     * @return mixed
     */
    public function getTxSeqID() {
        $v = $this->getCol("sequences")->findOneAndUpdate(['appid'=>1,'type'=>'txid'],['$inc'=>['seq'=>1]],['upsert' => true]);
        return "txid-".($v->seq);
    }

    public function tbl($app) {
        return $this->getCol($app."_".static::$table);
    }




    public function getSession() {
        if($this->sync) {
            return $this->client->startSession();
        } else {


        return MongoClient::getInstance()->client()->callback(function (MongoDriver $driver) {
            return $driver->getClient()->startSession();
        });
        }
    }
    public function getTbl() {
        return static::$table;
    }


    public function getOne($filter,$options=[]) {
        return $this->findOne($filter,$options);
    }

    public function findOne($filter = [], array $options = []) {
        $table = $this->getTbl();
        if($this->sync) {
            return $this->getCol($table)->findOne($filter , $options);

        } else {
            return MongoClient::getInstance()->client()->callback(function (MongoDriver $driver) use($filter,$options,$table){
                return $driver->getDb()->{$table}->findOne($filter , $options);
            });
        }

    }

    public function insertOne($data) {
        $table = $this->getTbl();
        if($this->sync) {
            $ret = $this->getCol($table)->insertOne($data);
            if($ret != null) {
                return $ret->getInsertedId();
            } else {
                return null;
            }
        } else {


        return MongoClient::getInstance()->client()->callback(function (MongoDriver $driver) use($data,$table){
            $ret =  $driver->getDb()->{$table}->insertOne($data);
            if($ret != null) {
               return $ret->getInsertedId();

            } else {
                return null;
            }
        });
        }
    }
    //
    public function updateOne($filter,$data,$options=[]) {
        $table = $this->getTbl();
        if($this->sync) {

            $ret = $this->getCol($table)->updateOne($filter,$data,$options);
            if($ret != null) {
                return $ret->getModifiedCount();
            } else {
                return 0;
            }
        } else {


            return MongoClient::getInstance()->client()->callback(function (MongoDriver $driver) use($filter,$data,$options,$table){
                $ret =  $driver->getDb()->{$table}->updateOne($filter,$data,$options);

                if($ret != null) {
                    return $ret->getModifiedCount();
                } else {
                    return 0;
                }
            });
        }
    }

    /*
     * {"errno":0,"data":{"total":0,"pages":0,"limit":20,"page":0,"list":[]},"errmsg":"成功"}
     */
    public function search($filter,$options=[]) {
        $table = $this->getTbl();

        if($this->sync) {
            $count = $this->getCol($table)->countDocuments($filter);
            $cursor =  $this->getCol($table)->find($filter,$options);
            $list=[];
            foreach ($cursor as $o) {
                $o['oid'] = (string)$o["_id"];
                unset($o["_id"]);
                $list[]=$o;
            }
            return ['total'=>$count,'list'=> $list];

        } else {
            return MongoClient::getInstance()->client()->callback(function (MongoDriver $driver) use($filter,$options,$table){
                $count = $driver->getDb()->{$table}->countDocuments($filter);
                $cursor =  $driver->getDb()->{$table}->find($filter,$options);
                $list=[];
                foreach ($cursor as $o) {
                    $o['oid'] = (string)$o["_id"];
                    unset($o["_id"]);
                    $list[]=$o;
                }
                return ['total'=>$count,'list'=> $list];

            });
        }

    }

    public function gets($filter=[],$options=[]) {
        $table = $this->getTbl();
        if($this->sync) {
            $cursor =  $this->getCol($table)->find($filter,$options);
            return iterator_to_array($cursor);
        } else {
            return MongoClient::getInstance()->client()->callback(function (MongoDriver $driver) use($filter,$options,$table){
                $cursor =  $driver->getDb()->{$table}->find($filter,$options);
                return iterator_to_array($cursor);
            });
        }


    }




    /**
     * 检查Money是不是对哦图
     * @param $money
     * @return array
     */
    public function checkMoney($money) {
        if($money < 0.0 && $money > 1000000) {
            return ['code'=>1,"msg"=>'转出的钱不对'];
        } else {
            return ['code'=>0,"msg"=>'转出的钱不对'];
        }
    }




//    public function getSession()
//    {
//        MongoDB::defer('mongo')->getClient()->startSession();
//
//    }

    public function getDB() {
        return $this->db;
//        return MongoDB::defer("mongo")->getDB();

    }

    public function toArrayAndUnsetId($curosr) {
        $list=[];
        foreach($curosr as $o) {
            unset($o['_id']);
            $list[]=$o;
        }
        return $list;
    }

    public function getString($v) {
        if(empty($v)) {
            return "";
        }
        return strval($v);
    }
    public function getInt($v) {
        if(empty($v)) {
            return 0;
        }
        return intval($v);
    }
    public function getFloat($v) {
        if(empty($v)) {
            return 0;
        }
        return floatval($v);
    }

    public function getCol($colName="") {
        if($colName == "") {
            $colName = static::$table;
        }
        return $this->db->{$colName};

    }



    public function unsetOid(&$list) {
        foreach($list as &$o) {
            unset($o['_id']);
        }
    }


    public function getLoginInfo($accessToken)
    {
        $redis = CacheTools::getRedis();
        $v = $redis->get($accessToken);
        return json_decode($v,true);
    }

    public function getAccessTokenInfo($accessToken){
        return $this->getLoginInfo();
    }




    public function randomeSeq($app,$type,$max=13) {
        $inc = mt_rand(1,$max);
        $v = $this->getCol("platform_seq")->findOneAndUpdate(['app'=>$app,'type'=>$type],['$inc'=>['seq'=>$inc]],['upsert' => true,
            'returnDocument'=>2]);
//        var_dump($v);
        if(empty($v)) {
            $v = $this->getCol("platform_seq")->findOne(['app'=>$app,'type'=>$type]);
        }
        return $v['seq'];

    }


    public function appSeq($app,$type) {
        $v = $this->getCol("platform_seq")->findOneAndUpdate(['app'=>$app,'type'=>$type],['$inc'=>['seq'=>1]],['upsert' => true,
            'returnDocument'=>2]);
//        var_dump($v);
        if(empty($v)) {
            $v = $this->getCol("platform_seq")->findOne(['app'=>$app,'type'=>$type]);
        }
        return $v['seq'];

    }




    public function getFields(){
        if($this->fields) {
            return $this->fields;
        } else {
            return [];
        }
    }



    /**
     * 得到限制字段
     *
     * @param array $field
     * @param bool  $_id
     *
     * @return array
     */
    public function getFieldOption($field = [], $_id = false){
        $info = [];
        if($field){
            foreach ($field as $v){
                $info[$v] = 1;
            }
        }
        $info['_id'] = $_id ? 1 : 0;
        return [
            'projection' => $info
        ];
    }

    public function filterFields(&$data){
        $fields = static::$fields;
//        var_dump($fields);
//        var_dump($data);
        foreach($data as $k=>$v) {
            if(!isset($fields[$k])) {
                unset($data[$k]);
            }
        }
    }




    public function trackEvt(EvtBean $evtBean) {
        TaskManager::getInstance()->async(function () use ($evtBean){
            EventModel::getInstance()->record($evtBean);
        });

    }


    public function typeConvert(&$data) {

        foreach(static::$fields as $k=>$v) {
            if(!empty($v) && isset($v['func'])) {
                if(isset($data[$k])) {
                    $data[$k] = call_user_func($v['func'],$data[$k]);
                }
            }
        }
    }

    public function filterEmptyFieilds(&$data) {
        foreach($data as $k=>$v) {
            if(empty($v)) {
                track_info("{$k} 设置为空 ");
                unset($data[$k]);
            }
        }
    }

    public function dealLoc($loc) {
        $items = preg_split("/,/",$loc);
        if(count($items)==2) {
            $items[0] = floatval($items[0]);
            $items[1] = floatval($items[1]);
            return [
                "type"=>"Point",
                 "coordinates"=>$items
            ];

        } else {
            return [];
        }

    }
    public function __destruct()
    {
    }


    /**
     * @param $app
     * @param $uid
     * @param BaseBean $bean
     * @return array
     * 统统修改和删除
     */
    public function commmonUpsert($app,$uid, $bean, $callback=null) {
        $data = $bean->toArray();
        $data['app'] = $app;
        $this->typeConvert($data);

        if($callback) {
            $data = call_user_func($callback,$data);
        }

        if(!empty($bean->id)) {

            $data['update_at'] = ts();
            $this->filterEmptyFieilds($data);

            $data['updator'] = $uid;
            $ret = $this->tbl($app)->updateOne(['id'=>intval($bean->id)],['$set'=>$data]);
            if($ret->getModifiedCount() == 1) {
                return $this->setRet(0,'succ',$data);
            } else {
                return $this->setRet(1,'fail',$data);
            }

        } else {
            $data['create_at'] = ts();
            $data['updator'] = $uid;
            $data['creator'] = $uid;
            $data['id'] = $this->appSeq($app,$this->getTbl());
            $ret = $this->tbl($app)->insertOne($data);
            if($ret->getInsertedCount() ==1 ){
                return $this->setRet(0,'succ',$data);
            } else {
                return $this->setRet(1,'fail',$data);
            }
        }
    }


    public function locToStr(&$item) {
        if(is_array($item['loc']) && !empty($item['loc']['coordinates'])) {
            $item['loc'] = $item['loc']['coordinates'][0].",".$item['loc']['coordinates'][1];
        }
    }

    public function getOneRet($app,$filter,$option=[]) {
        $vo = $this->tbl($app)->findOne($filter,$option);
        if($vo) {
            return $this->setRet(0,'succ',$vo);
        } else {
            return $this->setRet(1,'get fail',$filter);
        }
    }



    public function log($app,$item,$ip="") {
        OperationLogModel::getInstance()->log($app,$item,$ip);

    }

    public function getProjection($str) {
        $items = preg_split("/,/",$str);
        $vo=[];
        foreach($items as $item) {
            $vo[$item]=1;
        }
        return ['projection'=>$vo];
    }

}


