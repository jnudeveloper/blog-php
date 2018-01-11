<?php
/**
 * Created by PhpStorm.
 * User: Darkgel
 * Date: 2018/1/7
 * Time: 14:42
 */

namespace console\controllers;

use common\service\PostService;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TMultiplexedProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;
use thriftgen\domain\TQuery;
use thriftgen\service\AdditionServiceClient;
use thriftgen\service\MultiplicationServiceClient;
use yii\console\Controller;
use common\service\AdditionService;
use common\service\MultiplicationService;
use console\models\Post;
use src\thrift\base\Query;

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

            $transport->open();

            $additionProtocol = new TMultiplexedProtocol($protocol, "AdditionService");
            $multiplicationProtocol =new TMultiplexedProtocol($protocol, "MultiplicationService");

            $additionClient = new AdditionServiceClient($additionProtocol);
            $multiplicationClient = new MultiplicationServiceClient($multiplicationProtocol);

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

    public function actionTest1(){
        $addResult = AdditionService::getInstance()->add(2, 3);
        print 'result of 2 + 3 : '.$addResult.PHP_EOL;

        $multiResult = MultiplicationService::getInstance()->multiply(2, 3);
        print 'result of 2 * 3 : '.$multiResult.PHP_EOL;
        print 'end';
    }

    public function actionPost(){
        $query = new Query([
            'where' => [
                'title' => 'title',
                'status' => 1
            ],
        ]);

        $result = PostService::getInstance()->find($query->format());

        var_dump($result);

    }
}