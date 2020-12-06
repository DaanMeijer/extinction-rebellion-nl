<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RedisCache
 *
 * @author tbilou
 */
require_once 'iCache.php';

class RedisCache implements iCache {

    var $cache_expire = null;
    protected $REDIS_HOST = '127.0.0.1';
    protected $REDIS_PORT = 6379;
    protected $REDIS_KEY = "TB.Hash.Cache";

    //put your code here
    public function __construct() {

        $this->redis = new Redis() or die("Can't load redis module.");
        $this->redis->connect($this->REDIS_HOST, $this->REDIS_PORT);
    }

    function __destruct() {
        $this->redis->close();
    }

    public function cache($request, $response) {
        // Store the response on the cache
        $key = md5(serialize($request));
        $this->redis->hset($this->REDIS_KEY, $key . "created", time());
        return $this->redis->hset($this->REDIS_KEY, $key, $response);
    }

    public function getCached($request) {
        $key = md5(serialize($request));
        $created = $this->redis->hget($this->REDIS_KEY, $key . "created");
        if (($created + $this->cache_expire) < time()) {
            return;
        } else {
            return $this->redis->hget($this->REDIS_KEY, $key);
        }
    }

}

?>
