<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author tbilou
 */
interface iCache {
    public function cache($request, $response);
    public function getCached($request);
}

?>
