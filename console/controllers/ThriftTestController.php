<?php
/**
 * Created by PhpStorm.
 * User: Darkgel
 * Date: 2018/1/7
 * Time: 14:42
 */

namespace console\controllers;

use Thrift\ClassLoader\ThriftClassLoader;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TMultiplexedProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;
use common\thrift\gen\service\AdditionServiceClient;
use common\thrift\gen\service\MultiplicationServiceClient;
use yii\console\Controller;

class ThriftTestController extends Controller
{
    public function actionTest(){
        if(php_sapi_name() == 'cli'){
            ini_set("display_errors", "stderr");
        }

        try{
            $socket = new TSocket('localhost', 7911);
            $transport = new TBufferedTransport($socket, 1024, 1024);
            $protocol = new TBinaryProtocol($transport);

            $additionProtocol = new TMultiplexedProtocol($protocol, "AdditionService");
            $multiplicationProtocol =new TMultiplexedProtocol($protocol, "MultiplicationService");

            $additionClient = new AdditionServiceClient($additionProtocol);
            $multiplicationClient = new MultiplicationServiceClient($multiplicationProtocol);

            $transport->open();

            $addResult = $additionClient->add(1, 2);
            $multiplicationResult = $multiplicationClient->multiply(2, 3);

            print "the result of 1 + 2 : ".$addResult.PHP_EOL;
            print "the result of 2 * 3 : ".$multiplicationResult.PHP_EOL;

            $transport->close();

        } catch (TException $e){
            print 'TException：'.$e->getMessage().'\n';
            print 'TException: '.$e->getTraceAsString().'\n';
        }

    }
}