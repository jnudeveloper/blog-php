<?php
/**
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2017/12/18
 * Time: 19:58
 */

namespace common\components;

use yii\base\Component;
use Thrift\Transport\TSocket;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TMultiplexedProtocol;
use Thrift\Exception\TException;
use yii\base\Exception;
use Yii;

class ThriftManager extends Component
{
    public $serverHost = 'localhost';
    public $serverPort = '9090';
    public $sendTimeout = 5;
    public $recvTimeout = 5;

    public $namespacePrefix = 'common\\thrift\\gen\\';
    public $singleServiceConnectionConfig = [];
    public $multipleServiceConnectionConfig = [];

    private $services = [
        'singleServiceConnection' => [],
        'multipleServiceConnection' => [],
    ];

    const MULTIPLEXED_PROTOCOL = 2;

    public function init()
    {
        foreach($this->singleServiceConnectionConfig as $k => $v)
        {
            $this->services['singleServiceConnection'][$k] = $k;
        }

        foreach($this->multipleServiceConnectionConfig as $socketStr => $config){
            $this->services['multipleServiceConnection'][$socketStr]['protocol'] = '';
            foreach ($config['services'] as  $serviceName => $configArray){
                $this->services['multipleServiceConnection'][$socketStr]['services'][$serviceName] = $serviceName;
            }
        }
    }

    public function __get($name)
    {
        $service = $this->getSingleService($name);
        if(!is_null($service)){
            return $service;
        }

        $service = $this->getMultipleService($name);
        if(!is_null($service)){
            return $service;
        }

        throw new Exception('Service Not Defined');
    }



    private function getSingleService($name){
        if(isset($this->services['singleServiceConnection'][$name])){
            if(is_string($this->services['singleServiceConnection'][$name]))
            {
                $config = $this->singleServiceConnectionConfig[$name];
                $configName = array('sendTimeout', 'recvTimeout', 'serverHost', 'serverPort', 'dirName','maxConnectTimes','className');
                foreach($configName as $cn)
                {
                    if(empty($config[$cn]))
                    {
                        $config[$cn] = $this->{$cn};
                    }
                }

                for($i=0;$i<$config['maxConnectTimes'];$i++){
                    try{
                        $transport = new TSocket($config['serverHost'], $config['serverPort']);
                        $transport->setSendTimeout($config['sendTimeout']);
                        $transport->setRecvTimeout($config['recvTimeout']);
                        $transport->open();
                        $protocol = new TBinaryProtocol(new TBufferedTransport($transport));
                        $client = $this->namespacePrefix.$config['dirName'].'\\'.$config['className'].'Client';
                        $this->services['singleServiceConnection'][$name] = new $client($protocol);
                        break;

                    }catch(TException $e){
                        Yii::error($e->getMessage().'   connection:'.$config['serverHost'].':'.$config['serverPort'], __METHOD__);
                        if($i == $config['maxConnectTimes']-1){
                            throw $e;
                        }
                    }
                }
            }
            return $this->services['singleServiceConnection'][$name];
        }

        return null;
    }

    private function getMultipleService($name){
        foreach ($this->services['multipleServiceConnection'] as $socketStr => $config){
            if(isset($this->services['multipleServiceConnection'][$socketStr]['services'][$name])){
                if(is_string($this->services['multipleServiceConnection'][$socketStr]['services'][$name])){
                    $protocol = $this->getCommonProtocol($socketStr);
                    $multiplexedProtocol = new TMultiplexedProtocol($protocol, $name);
                    $client = $this->namespacePrefix.$config['dirName'].'\\'.$config['className'].'Client';
                    $this->services['multipleServiceConnection'][$socketStr]['services'][$name] = new $client($multiplexedProtocol);
                }
                return $this->services['multipleServiceConnection'][$socketStr]['services'][$name];
            }
        }

        return null;
    }

    private function getCommonProtocol($socketStr){
        if(is_string($this->services['multipleServiceConnection'][$socketStr]['protocol'])){
            $config = $this->services['multipleServiceConnection'][$socketStr];
            $configName = array('sendTimeout', 'recvTimeout', 'serverHost', 'serverPort', 'dirName','maxConnectTimes');
            foreach($configName as $cn)
            {
                if(empty($config[$cn]))
                {
                    $config[$cn] = $this->{$cn};
                }
            }

            for($i=0;$i<$config['maxConnectTimes'];$i++){
                try{
                    $socket = new TSocket($config['serverHost'], $config['serverPort']);
                    $socket->setSendTimeout($config['sendTimeout']);
                    $socket->setRecvTimeout($config['recvTimeout']);
                    $transport = new TBufferedTransport($socket);
                    $this->services['multipleServiceConnection'][$socketStr]['protocol'] = new TBinaryProtocol($transport);
                    $transport->open();
                    //这里要不要注册一下shutdown函数
                    break;

                }catch(TException $e){
                    Yii::error($e->getMessage().'   connection:'.$config['serverHost'].':'.$config['serverPort'], __METHOD__);
                    if($i == $config['maxConnectTimes']-1){
                        throw $e;
                    }
                }
            }
        }

        return $this->services['multipleServiceConnection'][$socketStr]['protocol'];
    }
}