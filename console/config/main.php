<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
//        'thriftManager' => [
//            'class' => 'common\components\ThriftManager',
//            'multipleServiceConnectionConfig' => [
//                'localhost:7911' => [
//                    'services' => [
//                        'AdditionService' => [
//                            'sendTimeout' => 100,
//                            'recvTimeout' => 100,
//                            'serverHost' => 'localhost',
//                            'serverPort' => 7911,
//                            'dirName' => '',
//                            'maxConnectTimes' => 3,
//                        ],
//                        'MultiplicationService' => [
//                            'sendTimeout' => 100,
//                            'recvTimeout' => 100,
//                            'serverHost' => 'localhost',
//                            'serverPort' => 7911,
//                            'dirName' => '',
//                            'maxConnectTimes' => 3,
//                        ],
//                    ],
//                ],
//            ],
//        ],
        'thriftManager' => [
            'class' => 'common\components\ThriftManager',
            'multipleServiceConnectionConfig' => [
                'localhost:7911' => [
                    'sendTimeout' => 3,
                    'recvTimeout' => 3,
                    'serverHost' => 'localhost',
                    'serverPort' => 7911,
                    'maxConnectTimes' => 2,
                    'services' => [
                        'AdditionService' => [
                            'dirPath' => 'service\\',
                            'className' => 'AdditionService',
                        ],
                        'MultiplicationService' => [
                            'dirPath' => 'service\\',
                            'className' => 'MultiplicationService',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
/*
 * common\components\ThriftManager的$services(init后)
[
    'singleServiceConnection' => [],
    'multipleServiceConnection' => [
        'localhost:7911' => [
            'protocol' => '',
            'services' => [
                'AdditionService' => 'AdditionService',
                'MultiplicationService' => 'MultiplicationService',
            ]
        ],
    ],
];

调用一次服务后（例如AdditionService）
[
    'singleServiceConnection' => [],
    'multipleServiceConnection' => [
        'localhost:7911' => [
            'protocol' => $binaryProtocol,//common protocol
            'services' => [
                'AdditionService' => $client,
                'MultiplicationService' => 'MultiplicationService',
            ]
        ],
    ],
];



*/
