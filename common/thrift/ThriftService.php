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

abstract class ThriftService{
    //对应后端相应的service名称
    public $service = '';

    //是否使用了多路协议，是则为true，否则为false
    public $multiplexed = false;

    //返回特定的client,由thrift生成
    protected $thriftClient = null;

    protected $errCode = null;

    protected $errMsg = '';

    protected $data = null;

    protected function __construct() {
        $this->init();
    }

    //禁止克隆
    protected final function __clone() {}

    protected function init() {
        if($this->service == ''){
            throw new Exception('service is invalid', Code::CONF_MISS);
        }

        try{
            $this->thriftClient = Yii::$app->thriftManager->getService($this->service, $this->multiplexed);

        }catch(Exception $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            $this->thriftClient = null;
            Yii::error($this->service.'['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
        }
    }

    public final static function getInstance() {
        if(static::$instance === null){
            static::$instance = new static();
        }
        return static::$instance;
    }

    //获取所有数据
    public function getData(){
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

    //重置errCode,errMsg,data
    protected function reset(){
        $this->errCode = null;
        $this->errMsg = '';
        $this->data = null;
    }

    public function __call($name, $arguments)
    {
        $this->reset();

        if($this->invoke($name, $arguments)){
            return $this->getData();
        }else{
            return false;
        }
    }

    //调用接口
    public function invoke($method, $arguments){
        if($this->thriftClient == null){
            return false;
        }

        //取得调用的方法信息
        $methodInfo = new \ReflectionMethod($this->thriftClient, $method);
        $data = array();
        foreach($methodInfo->getParameters() as $object){
            $data[] = $object->getName();
        }

        //检验参数是否正确
        try{
            $error = call_user_func_array(array($this,'check'), array($method,array_combine($data, $arguments)));
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

        //调用service中的方法
        try{
            $this->data = call_user_func_array(array($this->thriftClient,$method),$arguments);
        }catch(TTransportException $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            Yii::error('TTransportException : '.$this->service.'/'.$method.':'.'['.json_encode($arguments,JSON_UNESCAPED_UNICODE).']['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }catch(TException $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            Yii::error('TException : '.$this->service.'/'.$method.':'.'['.json_encode($arguments,JSON_UNESCAPED_UNICODE).']['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }catch(Exception $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            Yii::error($this->service.'/'.$method.':'.'['.json_encode($arguments,JSON_UNESCAPED_UNICODE).']['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }

        Yii::info($this->service.'/'.$method.':'.'['.json_encode($arguments,JSON_UNESCAPED_UNICODE).']['.json_encode($this->data,JSON_UNESCAPED_UNICODE).']',__METHOD__);
        return true;
    }

    //获取检查数据的form model(获取数据检查模型)
    public function getFormModel($sign=''){
        return null;
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


}