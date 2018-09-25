<?php
/**
 * Created by vary.
 * User: ASUS80
 * Date: 2018/5/28
 * Time: 18:16
 */

namespace vary;

use Exception;
use Illuminate\Support\Facades\Redis;
use RedisException;

/**
 *  Redis锁操作类
 *  Date:   2016-06-30
 *  Author: fdipzone
 *  Ver:    1.0
 *
 *  Func:
 *  public  lock    获取锁
 *  public  unlock  释放锁
 *  private connect 连接
 */
class MyRedisLock
{ // class start

    private $_config;
    public $_redis;

    /**
     * 初始化
     * @param Array $config redis连接设定
     */
    public function __construct($config = array())
    {
        $this->_config = $config;
        $this->_redis = $this->connect();
    }

    /**
     * 获取锁
     * @param  String $key 锁标识
     * @param  Int $expire 锁过期时间
     * @return Boolean
     */
    public function lock($key, $expire = 5)
    {
        $is_lock = $this->_redis->set($key, time() + $expire, 'ex', $expire, 'nx');

        // 不能获取锁
        if (!$is_lock) {

            // 判断锁是否过期
            $lock_time = $this->_redis->get($key);

            // 锁已过期，删除锁，重新获取
            if (time() > $lock_time) {
                $this->unlock($key);
                $is_lock = $this->_redis->set($key, time() + $expire, 'ex', $expire, 'nx');
            }
        }

        return $is_lock ? true : false;
    }

    /**
     * 释放锁
     * @param  String $key 锁标识
     * @return Boolean
     */
    public function unlock($key)
    {
        return $this->_redis->del($key);
    }

    /**
     * 创建redis连接
     * @return Link
     */
    public function connect()
    {
        try {
            $redis = Redis::connection();
//            $redis = new \Redis();
// redis 没有设置密码
//            $redis->connect('127.0.0.1', 6379);

//            $redis->connect($this->_config['host'],$this->_config['port'],$this->_config['timeout'],$this->_config['reserved'],$this->_config['retry_interval']);
//            if(empty($this->_config['auth'])){
//                $redis->auth($this->_config['auth']);
//            }
//            $redis->select($this->_config['index']);
            return $redis;

        } catch (RedisException $e) {
            throw new Exception($e->getMessage());
            return false;
        }
    }

}

