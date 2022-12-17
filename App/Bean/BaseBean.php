<?php


namespace App\Bean;


use EasySwoole\Spl\SplBean;

class BaseBean extends SplBean
{

    protected $rule_set=[];

    public function isInsertValidate()
    {

        foreach ($this->rule_set as $rule) {
            $field = $rule['field'];
            $v = $this->getProperty($field);
            if(isset($rule['min'])) {
                if($v < $rule['min']) {
                    return false;
                }
            }
            if(isset($rule['max'])) {
                if($v > $rule['max']) {
                    return false;
                }
            }
            if(isset($rule['min_len'])) {
                if(strlen($v) < $rule['min_len']) {
                    return false;
                }
            }
            if(isset($rule['max_len'])) {
                if(strlen($v) > $rule['max_len']) {
                    return false;
                }
            }
            if(isset($rule['max_len'])) {
            }


            }
    }
}