<?php
/**
 * thrift组件配置
 *
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2017/12/18
 * Time: 20:04
 */

return [
    'class'=>'app\components\ThriftClient',
    'serviceConfig' => [
        //在此处配置各个client
        //example:
//        'ThriftProductInfoService' => [
//            'dirName' => 'crm',
//            'className' => 'ThriftProductInfoService',
//            'serverHost' => '10.252.131.80',
//            'serverPort' => 50211,
//            'sendTimeout' => 3000,
//            'recvTimeout' => 3000,
//            'maxConnectTimes' => 2,
//        ],
    ],
];