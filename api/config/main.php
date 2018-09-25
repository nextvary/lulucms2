<?php
$params = array_merge(
    require(__DIR__ . '/../../data/config/params.php'),
    require(__DIR__ . '/../../data/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'language' => 'zh-CN',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1'=>[
            'class'=>'api\modules\v1\Module'
        ],
        'v2'=>[
            'class'=>'api\modules\v2\Module'
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'source\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['admin/login'],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true, // 启用美化URL
            'enableStrictParsing' => false, // 是否执行严格的url解析
            'showScriptName' => false, // 在URL路径中是否显示脚本入口文件
            'rules' => [
                ['pattern'=>'index.php','route'=> '/'],
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
        	'class' => 'source\core\back\BackView',
        ],
        'request'=>[
            'class' => 'yii\web\Request',
            'enableCsrfValidation'=>false,
        ]
        
    ],
    'params' => $params,
];
