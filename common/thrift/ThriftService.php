<?php
/**
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2017/12/18
 * Time: 20:06
 */

namespace common\thrift;

use yii\base\Exception;
use common\util\Code;
use Yii;
use Thrift\Exception\TTransportException;
use Thrift\Exception\TException;


class ThriftService{
    //对应后端相应的service名称
    public $service = '';

    //返回特定的client,由thrift生成
    protected $thriftClient = null;

    protected $errCode = null;

    protected $errMsg = '';

    protected $data = null;

    public function __construct($service=''){
        $service != '' && $this->service = $service;
        if($service == '' && $this->service == ''){
            throw new Exception('service is invalid', Code::CONF_MISS);
        }

        $service = $this->service;
        try{
            $this->thriftClient = Yii::$app->thriftManager->$service;

        }catch(Exception $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            $this->thriftClient = null;
            Yii::error($this->service.'['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
        }
    }

    //获取所有数据
    public function data(){
        return $this->data;
    }

    public function hasError(){
        return is_null($this->errCode);
    }

    //获取错误码
    public function getErrCode(){
        return $this->errCode;
    }

    //获取错误信息
    public function getErrMsg(){
        return $this->errMsg;
    }

    //获取检查数据的form model(获取数据检查模型)
    public function getFormModel($sign=''){
        return null;
    }

    //重置errCode,errMsg,data
    protected function reset(){
        $this->errCode = null;
        $this->errMsg = '';
        $this->data = null;
    }

    //参数检查
    public function check($method,$data){
        //待检查参数
        $param = array();
        foreach($data as $key=>$value){
            if(is_object($value)){
                //参数是结构体，单独进行检查
                $classReflect = new \ReflectionClass($value);
                $name = $classReflect->getName();
                $m = $this->getFormModel($name);
                $odata = array();
                foreach($value as $k=>$v){
                    if(is_object($v)){
                        //进行递归检查自己的结构体
                        $errors = $this->check($method,array($k=>$v));
                        if(!empty($errors)){
                            return $errors;
                        }
                        continue;
                    }
                    $odata[$k] = $v;
                }
                if($m != null && ($m->attributes = $odata) && !$m->validate()){
                    return $m->getErrors();
                }
                continue;
            }
            $param[$key] = $value;
        }
        if(empty($param)){
            return array();
        }
        $mmethod = $this->getFormModel($method);
        if($mmethod != null && ($mmethod->attributes = $param) && !$mmethod->validate()){
            return $mmethod->getErrors();
        }
        return array();
    }

    //调用接口
    public function invoke(){
        if($this->thriftClient == NULL){
            return False;
        }
        $this->reset();
        $args = func_get_args();
        $method = array_shift($args);

        //取得调用的方法信息
        $methodInfo = new \ReflectionMethod($this->thriftClient, $method);
        $data = array();
        foreach($methodInfo->getParameters() as $object){
            $data[] = $object->getName();
        }
        try{
            $error = call_user_func_array(array($this,'check'), array($method,array_combine($data, $args)));
        }catch(Exception $e){
            $this->errCode = Code::PARAM_ERR;
            $this->errMsg = $e->getMessage();
            return false;
        }
        if(!empty($error)){
            $this->errCode = Code::PARAM_ERR;
            foreach($error as $key=>$value){
                $this->errMsg != '' && $this->errMsg .= ';';
                $this->errMsg .= $key.':'.implode(',', $value);
            }
            return false;
        }
        try{
            $data = call_user_func_array(array($this->thriftClient,$method),$args);
        }catch(TTransportException $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            Yii::error($this->service.'/'.$method.':'.'['.json_encode($args,JSON_UNESCAPED_UNICODE).']['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }catch(TException $e){
            $this->errCode = $e->errCode;
            $this->errMsg = $e->errMsg;
            Yii::error($this->service.'/'.$method.':'.'['.json_encode($args,JSON_UNESCAPED_UNICODE).']['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }catch(Exception $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            Yii::error($this->service.'/'.$method.':'.'['.json_encode($args,JSON_UNESCAPED_UNICODE).']['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }
        // TODO 是否转换？ by darkgel
        //$this->data = G::objectToArray($data);
        $this->data = $data;//by darkgel
        Yii::error($this->service.'/'.$method.':'.'['.json_encode($args,JSON_UNESCAPED_UNICODE).']['.json_encode($this->data,JSON_UNESCAPED_UNICODE).']',__METHOD__);
        return true;
    }


}