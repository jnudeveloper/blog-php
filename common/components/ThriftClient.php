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

class ThriftClient extends Component
{
    public $serverHost = 'localhost';
    public $serverPort = '9090';
    public $sendTimeout = 5;
    public $recvTimeout = 5;
    public $serviceConfig = array();

    private $services = array();

    const MULTIPLEXED_PROTOCOL = 2;

    public function init()
    {
        foreach($this->serviceConfig as $name=>$config)
        {
            $this->services[$name] = $name;
        }
    }

    public function __get($name)
    {
        if(isset($this->services[$name]))
        {
            if(is_string($this->services[$name]))
            {
                $config = $this->serviceConfig[$name];
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
                        $transport->setSendTimeout($config['sendTimeout'] * 1000);
                        $transport->setRecvTimeout($config['recvTimeout'] * 1000);
                        $transport->open();

                        if( $transport->isOpen() ){
                            $protocol = new TBinaryProtocol(new TBufferedTransport($transport));
                            //支持多路协议配置
                            if(isset($config['protocolType']) && $config['protocolType']==self::MULTIPLEXED_PROTOCOL){
                                $protocol = new TMultiplexedProtocol($protocol, $this->services[$name]);
                            }
                            $class = $config['dirName'].'\\'.$config['className'].'Client';
                            $this->services[$name] = new $class($protocol);
                            break;
                        }

                    }catch(TException $e){
                        //log
                        if($i == $config['maxConnectTimes']-1){
                            throw $e;
                        }
                    }
                }
            }

            return $this->services[$name];
        }
        else
        {
            throw new Exception('Service Not Defined');
        }
    }
}