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
use thriftgen\domain\Request;
use thriftgen\domain\Response;
use common\util\Helper;

abstract class ThriftService{
    //对应后端相应的service名称
    public $service = '';

    //是否使用了多路协议，是则为true，否则为false
    public $multiplexed = false;

    //返回特定的client,由thrift生成
    protected $thriftClient = null;

    protected $errCode = null;

    protected $errMsg = '';

    protected $response = null;

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

    /**
     * 返回service 单实例
     * @return ThriftService
    */
    public final static function getInstance() {
        if(static::$instance === null){
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function getResponse(){
        return $this->response;
    }

    public function hasError(){
        return is_null($this->errCode);
    }

    public function getError(){
        return [
            'errCode' => $this->errCode,
            'errMsg' => $this->errMsg,
        ];
    }

    //重置errCode,errMsg,response
    protected function reset(){
        $this->errCode = null;
        $this->errMsg = '';
        $this->response = null;
    }

    /**
     * 调用thrift client中的方法,调用成功返回true，否则返回false
     * @param string $method 即将调用的方法
     * @param Request $request 请求
     * @return bool
     */
    public function invoke($method, $request){
        $this->reset();

        if($this->thriftClient == null){
            $this->errCode = Code::THRIFT_FAIL;
            $this->errMsg = "thriftClient is null";
            Yii::error("thriftClient is null : ".$this->service.'['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }

        //调用thrift client中的方法
        try{
            $this->response = call_user_func_array(array($this->thriftClient,$method),[$request]);
        }catch(TTransportException $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            Yii::error('TTransportException : '.$this->service.'/'.$method.':'.'['.json_encode($request,JSON_UNESCAPED_UNICODE).']['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }catch(TException $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            Yii::error('TException : '.$this->service.'/'.$method.':'.'['.json_encode($request,JSON_UNESCAPED_UNICODE).']['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }catch(Exception $e){
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
            Yii::error($this->service.'/'.$method.':'.'['.json_encode($request,JSON_UNESCAPED_UNICODE).']['.$this->errCode.':'.$this->errMsg.']',__METHOD__);
            return false;
        }

        Yii::info($this->service.'/'.$method.':'.'['.json_encode($request,JSON_UNESCAPED_UNICODE).']['.json_encode($this->response,JSON_UNESCAPED_UNICODE).']',__METHOD__);
        return true;
    }

    /**
     * 调用thrift服务的相应方法
     * @param string $dstMethod 即将调用的方法（目标方法）
     * @param Request $request 请求
     * @param string $srcMethod 源方法，记录日志用
     * @return false|array
     */
    protected static function call($dstMethod, $request, $srcMethod){
        if(static::getInstance()->invoke($dstMethod, $request)){
            //调用成功
            $response = static::getInstance()->getResponse();
            return static::getInstance()->processResponse($response, $srcMethod);
        }else{
            //调用失败,在PostService等服务实例中可以通过(PostService)self::getInstance()->getError()获取错误信息
            return false;
        }
    }

    /**
     * 对响应进行处理
     * @param Response|false $response
     * @param string $method the string indicate the method
     * @return false|array
     */
    protected function processResponse($response, $method){
        if($response === false){
            return false;
        }

        if($response->code !== 0){
            Yii::error('get error response through thrift : '.$response->msg.'[errCode:'.$response->code.']', $method);
            $this->errCode = $response->code;
            $this->errMsg = $response->msg;
            return false;
        }else{
            return json_decode($response->data, true);
        }
    }

    /**
     * 构造请求的通用数据
     * @return Request
     */
    protected static function getRequest(){
        $request = new Request();
        $request->clientIp = Yii::$app->params['localIp'];
        $request->appId = Yii::$app->params['appId'];
        $request->appKey = Yii::$app->params['appKey'];
        $request->requestTime = time();
        $request->requestId = $request->appId.'-'.$request->requestTime;

        return $request;
    }

}