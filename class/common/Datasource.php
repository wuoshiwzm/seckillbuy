<?php

/**
 * Class Datasource 官方推荐 phpredis / Predis
 */

class Datasource{
    public static $redises = array();
    public static $caches = array();

    public function __construct()
    {
        
    }

    public static function getRedis($config_name = NULL, $server_region = 'default')
    {
        if($config_name === NULL){
            return;
        }

        if(isset(self::$redises[$config_name]) && self::$redises[$config_name]){
            return self::$redises[$config_name];
        }
        global $config;
        //根据$config_name 拿redis.ini.php中的配置信息
        $redis_config = $config['redis'][$config_name];
        try{
            self::$redises[$config_name] = RedisHelper::instance($config_name,$redis_config,$server_region);
        }catch (Exception $e){
            self::$redises[$config_name] = null;
        }
        return self::$redises[$config_name];

    }

    public static function getCache($config_name = null,$server_region = 'default')
    {
        if(isset(self::$caches[$config_name]) && self::$caches[$config_name]){
            return self::$caches[$config_name];
        }
        if($config_name === null){
            return ;
        }

        global $config;
        $memcache_config = $config['cache'][$config_name];
        try{
            self::$caches[$config_name] = CacheHelper::instance($config_name,$memcache_config,$server_region);
        }catch(Exception $e){
            self::$caches[$config_name] = null;
        }
        return self::$caches[$config_name];

    }
}

class RedisHelper{
    private $_config_name = "";
    private $_redis_config = null;
    private $_server_region = null;
    public $timeout = 1;
    private $_redis = null;
    private static $instance = array();
    private static $connect_error = 0;
    private $call_error = 0;

    private function __construct( $config_name, $redis_config,$server_region)
    {
        if($config_name && $redis_config && $server_region){
            $this->_config_name = $config_name;
            $this->_redis_config = $redis_config;
            $this->_server_region = $server_region;
            $this->timeout = isset($this->_redis_config[$server_region]['timeout']) ?
                $this->_redis_config[$server_region]['timeout'] : $this->timeout;

            try{
                $this->_redis = new redis();
                $this->_redis->connect($this->_redis_config[$server_region['host']],
                    $this->_redis_config[$server_region]['port'],$this->timeout);
                if($this->_redis_config[$server_region] &&
                    !$this->_redis->auth($this->_redis_config[$server_region]['password'])){
                    $this->_redis = null;
                }

            }catch (Exception $e){
                $this->_redis = null;
            }

        }else{
            $this->_redis = null;
        }
    }

    public function instance($config_name, $redis_config, $server_region)
    {
        if (!$config_name || !$redis_config) {
            return false;
        }
        $starttime = microtime();
        $only_key = $config_name . ':' . $server_region;
        if (!isset(self::$instances[$only_key])) {
            try {
                self::$instances[$only_key] = new RedisHelper($config_name, $redis_config, $server_region);
                self::$connect_error = 0;
            } catch (Exception $e) {
                // 连接失败后进行一定次数的重连
                if (self::$connect_error < 2) {
                    self::$connect_error += 1;
                    return RedisHelper::instance($config_name, $redis_config, $server_region);
                } else {
                    self::$connect_error = 0;
                    self::$instances[$only_key] = new RedisHelper(false, false, false);
                }
            }
        }
        $redis_config_info = array();
        if ($redis_config && isset($redis_config[$server_region]) && isset($redis_config[$server_region]['password'])) {
            $redis_config_info = $redis_config[$server_region];
            //把password 信息抹掉了
            unset($redis_config_info['password']);
        }
        \common\DebugLog::_redis('redis_instance', $config_name, $redis_config_info, $starttime, microtime(), null);
        self::$connect_error = 0;
        return self::$instances[$only_key];
    }


    /**魔术方法
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if(!$this->_redis){
            return false;
        }
        $starttime = microtime();

        try{
            //把scan单独拿出来
            if('scan' === $name){
                $data = $this->_redis->scan($arguments[0]);
            }else{
                //对象 ：$this->_redis   方法名： $name
                $data = call_user_func_array(array($this->_redis, $name),$arguments);
            }
        }catch(Exception $e){
            if ($this->call_error < 2) {
                $this->call_error++;
                return call_user_func_array(array($this->_redis, $name), $arguments);
            } else {
                $this->call_error = 0;
            }
            $data = false;
        }
        $this->call_error = 0;
        $redis_config = $this->_redis_config[$this->_server_region];
        if ($redis_config && isset($redis_config['password'])) {
            unset($redis_config['password']);
        }
        \common\DebugLog::_redis($name, $arguments, $redis_config, $starttime, microtime(), (is_string($data) || is_array($data)) ? $data : null);
        return $data;


    }





}















































































