<?php
    session_start();
    /* Last updated with phpFlickr 1.4
     *
     * If you need your app to always login with the same user (to see your private
     * photos or photosets, for example), you can use this file to login and get a
     * token assigned so that you can hard code the token to be used.  To use this
     * use the phpFlickr::setToken() function whenever you create an instance of 
     * the class.
     */

    require_once 'Credentials.php';
    require_once("phpFlickr.php");

    $callback = 'http://www.flickr.dev/callBack.php';
    
    $f = new phpFlickr(Credentials::API_KEY, Credentials::API_SECRET, true);
    $f->getRequestToken($callback); 

    echo $f->getErrorCode()."". $f->getErrorMsg();
?>