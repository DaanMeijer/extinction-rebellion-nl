<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileCache
 *
 * @author tbilou
 */
require_once 'iCache.php';

class FileCache implements iCache {

    var $cache_dir = null;
    var $cache_expire = null;
    var $cache_key;
    var $cache_request;
    var $php_version;

    //put your code here
    public function __construct() {
        $this->php_version = explode("-", phpversion());
        $this->php_version = explode(".", $this->php_version[0]);

        $connection = realpath(sys_get_temp_dir());
        $this->cache_dir = $connection;

    }

    public function cache($request, $response) {
        //Caches the unparsed response of a request.
        unset($request['api_sig']);
        foreach ($request as $key => $value) {
            if (empty($value))
                unset($request[$key]);
            else
                $request[$key] = (string) $request[$key];
        }
        $reqhash = md5(serialize($request));

        $file = $this->cache_dir . "/" . $reqhash . ".cache";
        $dirname = dirname($file);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
        $fstream = fopen($file, "w");
        $result = fwrite($fstream, $response);
        fclose($fstream);
        return $result;
    }

    public function getCached($request) {

        foreach ($request as $key => $value) {
            if (empty($value))
                unset($request[$key]);
            else
                $request[$key] = (string) $request[$key];
        }

        $reqhash = md5(serialize($request));
        $this->cache_key = $reqhash;
        $this->cache_request = $request;

        $file = $this->cache_dir . '/' . $reqhash . '.cache';
        if (file_exists($file)) {
            // Check if cache is still valid
            if (substr($file, -6) == '.cache' && ((filemtime($file) + $this->cache_expire) < time())) {
                //unlink($file);
                return;
            }
            if ($this->php_version[0] > 4 || ($this->php_version[0] == 4 && $this->php_version[1] >= 3)) {
                return file_get_contents($file);
            } else {
                return implode('', file($file));
            }
        }
    }

}

?>
