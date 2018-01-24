<?php
/**
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2018/1/9
 * Time: 17:12
 */

return [
    'class' => 'common\components\ThriftManager',
    'namespacePrefix' => 'thriftgen\\',
    'multipleServiceConnectionConfig' => [
        'localhost:7911' => [
            'sendTimeout' => 10,
            'recvTimeout' => 10,
            'serverHost' => 'localhost',
            'serverPort' => 7911,
            'maxConnectTimes' => 2,
            'services' => require_once __DIR__.'/blogServices.php',
        ],
    ],
];