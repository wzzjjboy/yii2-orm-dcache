<?php
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
    ],
    'components' => [
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'exportInterval' => 25,
                ],
//                [
//                    'class' => 'common\components\log\RedisTarget',
//                    'levels' => ['error', 'warning', 'info'],
//                    'redis' => 'log_redis',
//                    'application' => 'service_client_console',
//                    'log_key' => 'logstash:nginx',
//                    'exportInterval' => 25,
//                ],
//                [
//                    'class' => 'yii\log\FileTarget',
//                    'levels' => ['error', 'warning','info'],
//                    'logVars'=>[], //表示以yii\db\或者app\models\开头的分类都会写入这个文件
//                    'categories'=>['app\models\*', 'yii\db\Command::execute'], //表示写入到文件
//                    'logFile'=>'@runtime/logs/sql/'.date('ymd').'.log',
//                ],
            ],
        ],
    ],
    'params' => $params,
];
