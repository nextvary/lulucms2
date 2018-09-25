<?php
/**
 * Created by PhpStorm.
 * User: haifei
 * Date: 2017/6/30
 * Time: 10:30
 */
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require (__DIR__ . '/../vendor/autoload.php');
require (__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

require (__DIR__ . '/../source/override.php');

require (__DIR__ . '/../data/config/bootstrap.php');
require (__DIR__ . '/config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../data/config/main.php'),
    require(__DIR__ . '/../data/config/main-local.php'),
    require(__DIR__ . '/config/main.php'),
    require(__DIR__ . '/config/main-local.php')
);

$app = new yii\web\Application($config);
$app->run();