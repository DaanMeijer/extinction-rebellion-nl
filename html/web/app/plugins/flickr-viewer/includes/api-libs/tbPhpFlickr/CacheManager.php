<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class CacheManager {

    const CACHE_EXPIRE = 3600;
    var $cache = false;
    var $cache_db = null;
    var $cache_table = null;
    var $cache_dir = null;
    var $cache_expire = null;
    var $cache_key = null;
    var $memcache = null;

    /*
     * When your database cache table hits this many rows, a cleanup
     * will occur to get rid of all of the old rows and cleanup the
     * garbage in the table.  For most personal apps, 1000 rows should
     * be more than enough.  If your site gets hit by a lot of traffic
     * or you have a lot of disk space to spare, bump this number up.
     * You should try to set it high enough that the cleanup only
     * happens every once in a while, so this will depend on the growth
     * of your table.
     */
    var $max_cache_rows = 1000;
    
    var $php_version;

    function CacheManager() {
        $this->php_version = explode("-", phpversion());
	    $this->php_version = explode(".", $this->php_version[0]);

       // $this->enableCache('fs','/Users/iankennerley/Documents/sites/www.flickr.dev/cache');
    }

    function enableCache($type, $connection, $cache_expire = CacheManager::CACHE_EXPIRE, $table = 'flickr_cache') {

               //echo "Cache enabled...<br>";

        // Turns on caching.  $type must be either "db" (for database caching) or "fs" (for filesystem).
        // When using db, $connection must be a PEAR::DB connection string. Example:
        //	  "mysql://user:password@server/database"
        // If the $table, doesn't exist, it will attempt to create it.
        // When using file system, caching, the $connection is the folder that the web server has write
        // access to. Use absolute paths for best results.  Relative paths may have unexpected behavior
        // when you include this.  They'll usually work, you'll just want to test them.
        if ($type == 'db') {
//            if (preg_match('|mysql://([^:]*):([^@]*)@([^/]*)/(.*)|', $connection, $matches)) {
//                //Array ( [0] => mysql://user:password@server/database [1] => user [2] => password [3] => server [4] => database ) 
//                $db = mysql_connect($matches[3], $matches[1], $matches[2]);
//                mysql_select_db($matches[4], $db);
//
//                /*
//                 * If high performance is crucial, you can easily comment
//                 * out this query once you've created your database table.
//                 */
//                mysql_query("CREATE TABLE IF NOT EXISTS `$table` (
//                             `request` CHAR( 35 ) NOT NULL ,
//                             `response` MEDIUMTEXT NOT NULL ,
//                             `expiration` DATETIME NOT NULL ,
//                              INDEX ( `request` )
//                            ) TYPE = MYISAM
//			", $db);
//
//                $result = mysql_query("SELECT COUNT(*) FROM $table", $db);
//                $result = mysql_fetch_row($result);
//                if ($result[0] > $this->max_cache_rows) {
//                    mysql_query("DELETE FROM $table WHERE expiration < DATE_SUB(NOW(), INTERVAL $cache_expire second)", $db);
//                    mysql_query('OPTIMIZE TABLE ' . $this->cache_table, $db);
//                }
                $this->cache = 'db';
//                $this->cache_db = $db;
//                $this->cache_table = $table;
//            }
        } elseif ($type == 'fs') {
            $this->cache = 'fs';
            $connection = realpath($connection);
            $this->cache_dir = $connection;
            if ($dir = opendir($this->cache_dir)) {
                while ($file = readdir($dir)) {
                    if (substr($file, -6) == '.cache' && ((filemtime($this->cache_dir . '/' . $file) + $cache_expire) < time())) {
                        unlink($this->cache_dir . '/' . $file);
                    }
                }
            } else {
                die('Cannot write to "cache/" folder. Please make folder writable by your web server.');
            }
        } elseif($type == 'memcached')  {
            // Call memcache
            $this->memcache = new Memcache; // instantiating memcache extension class
            $this->memcache->connect("localhost",11211);
            $this->cache = "memcached";
            echo "Server's version: " . $this->memcache->getVersion() . "<br />\n";
        } elseif ($type == 'custom') {
            $this->cache = "custom";
            $this->custom_cache_get = $connection[0];
            $this->custom_cache_set = $connection[1];
        }
        $this->cache_expire = $cache_expire;
    }

    function getCached($request) {

// echo "Checking for cached version...<br>";

        //Checks the database or filesystem for a cached result to the request.
        //If there is no cache result, it returns a value of false. If it finds one,
        //it returns the unparsed XML.
        foreach ($request as $key => $value) {
            if (empty($value))
                unset($request[$key]);
            else
                $request[$key] = (string) $request[$key];
        }
        //if ( is_user_logged_in() ) print_r($request);
        $reqhash = md5(serialize($request));
        $this->cache_key = $reqhash;
        $this->cache_request = $request;
        if ($this->cache == 'db') {
            $result = mysql_query("SELECT response FROM " . $this->cache_table . " WHERE request = '" . $reqhash . "' AND DATE_SUB(NOW(), INTERVAL " . (int) $this->cache_expire . " SECOND) < expiration", $this->cache_db);
            if (mysql_num_rows($result)) {
                $result = mysql_fetch_assoc($result);
                return $result['response'];
            } else {
                return false;
            }
        } elseif ($this->cache == 'fs') {
            $file = $this->cache_dir . '/' . $reqhash . '.cache';

//echo "cache file: $file<br>";

            if (file_exists($file)) {

//echo "cached file found!!!!<br>";

                if ($this->php_version[0] > 4 || ($this->php_version[0] == 4 && $this->php_version[1] >= 3)) {
                    return file_get_contents($file);
                } else {
                    return implode('', file($file));
                }
            }
        } elseif($this->cache == 'memcached')  {
            // Call memcache
            return unserialize($this->memcache->get($reqhash));
            
        } elseif ($this->cache == 'custom') {
            return call_user_func_array($this->custom_cache_get, array($reqhash));
        }
        return false;
    }

    function cache($request, $response) {
        //Caches the unparsed response of a request.
        unset($request['api_sig']);
        foreach ($request as $key => $value) {
            if (empty($value))
                unset($request[$key]);
            else
                $request[$key] = (string) $request[$key];
        }
        $reqhash = md5(serialize($request));
        if ($this->cache == 'db') {
            //$this->cache_db->query("DELETE FROM $this->cache_table WHERE request = '$reqhash'");
            $result = mysql_query("SELECT COUNT(*) FROM " . $this->cache_table . " WHERE request = '" . $reqhash . "'", $this->cache_db);
            $result = mysql_fetch_row($result);
            if ($result[0]) {
                $sql = "UPDATE " . $this->cache_table . " SET response = '" . str_replace("'", "''", $response) . "', expiration = '" . strftime("%Y-%m-%d %H:%M:%S") . "' WHERE request = '" . $reqhash . "'";
                mysql_query($sql, $this->cache_db);
            } else {
                $sql = "INSERT INTO " . $this->cache_table . " (request, response, expiration) VALUES ('$reqhash', '" . str_replace("'", "''", $response) . "', '" . strftime("%Y-%m-%d %H:%M:%S") . "')";
                mysql_query($sql, $this->cache_db);
            }
        } elseif ($this->cache == "fs") {
            $file = $this->cache_dir . "/" . $reqhash . ".cache";
            $fstream = fopen($file, "w");
            $result = fwrite($fstream, $response);
            fclose($fstream);
            return $result;
         } elseif($this->cache == 'memcached')  {
            // Call memcache
            return $this->memcache->add($reqhash, serialize($response), 0, $this->cache_expire);
            
        } elseif ($this->cache == "custom") {
            return call_user_func_array($this->custom_cache_set, array($reqhash, $response, $this->cache_expire));
        }
        return false;
    }

}

?>
