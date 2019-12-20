<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.105.201.137;port=3307;dbname=providers',
            'username' => 'root',
            'password' => 'a6CFu3VH6OYW',
            'charset' => 'utf8',
            'tablePrefix' => 'gpi_',
            'commandClass' => 'common\components\db\Command',
        ],
        //权限中心
        'db_privilege' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.105.201.137;dbname=privilege',
            'username' => 'fpuser',
            'password' => 'PKuaRZUn7uJd',
            'charset' => 'utf8',
            'tablePrefix' => 'qx_',
            'commandClass' => 'common\components\db\Command',
        ],

        //抬头管理
        'db_title_manage' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.105.201.137;dbname=title_manage',
            'username' => 'dev',
            'password' => 'dev.pass',
            'charset' => 'utf8',
            'tablePrefix' => 'wx_',
            'commandClass' => 'common\components\db\Command',
        ],

        //管理后台
        'db_merchant' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.105.201.137;port=3306;dbname=fpmerchant',
            'username' => 'merchant',
            'password' => 'Fkl23Kws',
            'charset' => 'utf8',
            'tablePrefix' => 'm_',
            'commandClass' => 'common\components\db\Command',
        ],
        //工单
        'db_workorder' => [
            'class' => 'yii\db\Connection',
            'dsn'=>'mysql:host=10.105.201.137;port=3306;dbname=workorder',
            'username' => 'merchant',
            'password' => 'Fkl23Kws',
            'charset' => 'utf8',
            'tablePrefix' => 'wo_',
            'commandClass' => 'common\components\db\Command',
        ],

        //开票中心
        'fp_center' => [
            'class' => 'yii\db\Connection',
            'dsn'=>'mysql:host=10.105.201.137;port=3306;dbname=fpcenter',
            'username' => 'fpuser',
            'password' => 'PKuaRZUn7uJd',
            'charset' => 'utf8',
            'tablePrefix' => 'gp_',
            'commandClass' => 'common\components\db\Command',
        ],
        'redis' => [
            'class' => 'common\components\redis\Connection',
            'hostname' => '10.105.201.137',
            'port' => 7001,
            'database' => 1, //服务商
            'password' => 'pUD85cOEvX22'
        ],

        //发票同步
        'syncInvoice' => [
            'class'         => 'common\mqTask\tasks\SyncInvoice',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'ias.direct',
            'queue_name'    => 'ias.direct#service.invoice.sync',
            'routing_key'   => 'service.invoice.sync',
        ],

        'syncInvoiceFail' => [
            'class'         => 'common\mqTask\tasks\SyncInvoiceFail',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'ias.direct',
            'queue_name'    => 'ias.direct#service.invoice.sync.fail',
            'routing_key'   => 'service.invoice.sync.fail',
        ],

        'invoiceStatusUpdate' => [
            'class'         => 'common\mqTask\tasks\InvoiceStatusUpdate',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'bps.direct',
            'queue_name'    => 'bps.direct#user.invoice.apply',
            'routing_key'   => 'user.invoice.apply',
        ],
        'invoiceScore' => [
            'class'         => 'common\mqTask\tasks\InvoiceScore',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'bps.direct',
            'queue_name'    => 'bps.direct#user.invoice.score',
            'routing_key'   => 'user.invoice.score',
        ],
        //区块链发票同步
        'syncInvoiceBlock' => [
            'class'         => 'common\mqTask\tasks\SyncInvoiceBlock',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'ias.direct',
            'queue_name'    => 'ias.direct#service.invoice.sync.block',
            'routing_key'   => 'service.invoice.sync.block',
        ],
        'syncInvoiceFailBlock' => [
            'class'         => 'common\mqTask\tasks\SyncInvoiceFailBlock',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'ias.direct',
            'queue_name'    => 'ias.direct#service.invoice.sync.fail.block',
            'routing_key'   => 'service.invoice.sync.fail.block',
        ],
        //Tars新框架发票同步
        'syncInvoiceTars' => [
            'class'         => 'common\mqTask\tasks\SyncInvoiceTars',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'invoice.sync',
            'queue_name'    => 'invoice.sync#tars.invoice.sync',
            'routing_key'   => 'tars.invoice.sync',
        ],
        'syncInvoiceTarsFail' => [
            'class'         => 'common\mqTask\tasks\SyncInvoiceTarsFail',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'invoice.sync',
            'queue_name'    => 'invoice.sync#tars.invoice.sync.fail',
            'routing_key'   => 'tars.invoice.sync.fail',
        ],
        'syncInvoiceToNewSys' => [
            'class'         => 'common\mqTask\tasks\SyncInvoiceToNewSys',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'ias.direct',
            'queue_name'    => 'invoice.sync#old.to.new',
            'routing_key'   => 'old.to.new',
        ],
        'invoiceRedisEvent' => [
            'class'         => 'common\mqTask\tasks\InvoiceRedisEvent',
            'host'          => '10.154.33.130',
            'port'          => '5672',
            'username'      => 'rabbit',
            'password'      => 'aTjHMj7opZ3d5Kw6',
            'exchange_name' => 'invoice.event',
            'queue_name'    => 'invoice.event#from.redis',
            'routing_key'   => 'from.redis',
        ],
        'orderRedis' => [
            'class' => 'common\components\redis\Connection',
            'hostname' => '10.105.1.106',
            'port' => 6379,
            'database' => 0,
            'password' => '63KxsHOY4g939Apq'
        ],

        'db_tars_order' => [ //只取结构， 不查询，也不写数据，所以写到这里
             'class' => 'yii\db\Connection',
             'dsn' => 'mysql:host=10.105.1.106;port=3306;dbname=order_0',
             'username' => 'gordon',
             'password' => '4qYAEZ6scVNYPLTWRviT',
             'charset' => 'utf8',
             'enableSchemaCache' => true,
             'schemaCacheDuration' => 86400, // time in seconds
        ],
        'db_tars_relationship' => [ //只取结构， 不查询，也不写数据，所以写到这里
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.105.1.106;port=3306;dbname=relationship_0',
            'username' => 'gordon',
            'password' => '4qYAEZ6scVNYPLTWRviT',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 86400, // time in seconds
        ],
    ],
];