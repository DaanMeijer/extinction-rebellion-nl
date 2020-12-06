<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBCache
 *
 * @author tbilou
 */
class DBCache implements iCache {

    var $cache_dir = null;
    var $cache_expire = null;
    var $cache_db = null;
    var $cache_table = null;
    var $cache_key;
    var $cache_request;
    var $php_version;
    var $table = 'flickr_cache';
    var $max_cache_rows = 1000;

    //put your code here

    public function __construct() {
        if (preg_match('|mysql://([^:]*):([^@]*)@([^/]*)/(.*)|', $connection, $matches)) {
            //Array ( [0] => mysql://user:password@server/database [1] => user [2] => password [3] => server [4] => database ) 
            $db = mysql_connect($matches[3], $matches[1], $matches[2]);
            mysql_select_db($matches[4], $db);

            /*
             * If high performance is crucial, you can easily comment
             * out this query once you've created your database table.
             */
            mysql_query("CREATE TABLE IF NOT EXISTS `$this->table` (
                             `request` CHAR( 35 ) NOT NULL ,
                             `response` MEDIUMTEXT NOT NULL ,
                             `expiration` DATETIME NOT NULL ,
                              INDEX ( `request` )
                            ) TYPE = MYISAM
			", $db);

            $result = mysql_query("SELECT COUNT(*) FROM $this->table", $db);
            $result = mysql_fetch_row($result);
            if ($result[0] > $this->max_cache_rows) {
                mysql_query("DELETE FROM $table WHERE expiration < DATE_SUB(NOW(), INTERVAL $cache_expire second)", $db);
                mysql_query('OPTIMIZE TABLE ' . $this->cache_table, $db);
            }
            $this->cache = 'db';
            $this->cache_db = $db;
            $this->cache_table = $table;
        }
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

        $result = mysql_query("SELECT COUNT(*) FROM " . $this->cache_table . " WHERE request = '" . $reqhash . "'", $this->cache_db);
        $result = mysql_fetch_row($result);
        if ($result[0]) {
            $sql = "UPDATE " . $this->cache_table . " SET response = '" . str_replace("'", "''", $response) . "', expiration = '" . strftime("%Y-%m-%d %H:%M:%S") . "' WHERE request = '" . $reqhash . "'";
            mysql_query($sql, $this->cache_db);
        } else {
            $sql = "INSERT INTO " . $this->cache_table . " (request, response, expiration) VALUES ('$reqhash', '" . str_replace("'", "''", $response) . "', '" . strftime("%Y-%m-%d %H:%M:%S") . "')";
            mysql_query($sql, $this->cache_db);
        }
    }

    public function getCached($request) {
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

        $result = mysql_query("SELECT response FROM " . $this->cache_table . " WHERE request = '" . $reqhash . "' AND DATE_SUB(NOW(), INTERVAL " . (int) $this->cache_expire . " SECOND) < expiration", $this->cache_db);
        if (mysql_num_rows($result)) {
            $result = mysql_fetch_assoc($result);
            return $result['response'];
        } else {
            return false;
        }
    }

}

?>
