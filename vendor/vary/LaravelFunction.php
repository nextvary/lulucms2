<?php
/**
 * Created by vary.
 * User: ASUS80
 * Date: 2018/9/21
 * Time: 15:04
 */

namespace vary;


class LaravelFunction
{
    public static function RedisScan($pattern, $redis = null, $count = 100)
    {
        if ($redis == null) {
//            $redis=Redis::connection();
            $redis = $redis = new \Redis();
            $redis->connect('127.0.0.1', 6379);

        }
        $it = null;
        $pattern = $pattern ?: 'renfei*';
        $need = [];
        if ($redis instanceof \Redis) {
            $redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
            //如果是系统内置redis
            while ($keysarry = $redis->scan($it, $pattern, $count)) {
                foreach ($keysarry as $item) {
                    $need[] = $item;
                }
            }
        } else {
            while ($keysarry = $redis->scan($it, ['match' => $pattern, 'count' => $count])) {
                $it = $keysarry[0];
                if ($it == 0) {
                    break;
                }
                foreach ($keysarry[1] as $item) {
                    $need[] = $item;
                }
            }
        }
        return $need;
    }
}