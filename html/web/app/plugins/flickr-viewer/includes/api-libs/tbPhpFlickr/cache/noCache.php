<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of noCache
 *
 * @author tbilou
 */

require_once 'iCache.php';

class noCache implements iCache {

    //put your code here
    public function cache($request, $response) {
        return false;
    }

    public function getCached($request) {
        return false;
    }

}

?>
