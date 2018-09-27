<?php
/**
 * Created by vary.
 * User: ASUS80
 * Date: 2018/9/27
 * Time: 16:58
 */

namespace yii\console\controllers;


use Yii;
use yii\console\Controller;

class QueueController extends Controller
{
    /**
     * 测试
     */
    public function actionAdd()
    {
        $hashKey = 'caiyu';
        $field = 'nine';
        $val = 9;
        echo 1;
    }

    public function actionWork()
    {
        ini_set('default_socket_timeout', -1);  //避免在默认的配置下，1分钟后终端了与redis服务器的链接
        $redis = Yii::$app->redis;
        $redis->connect('127.0.0.1', 6379);
        $redis->subscribe(['task_queue'], function ($redis, $chan, $msg) {
            switch ($chan) {
                case 'task_queue':
                    $task = unserialize($msg);
                    if ($task['task'] == 'send_email') {
                        // send email
                    }
                    // do other job
                    break;
                default:
                    break;
            }
        });


    }
}