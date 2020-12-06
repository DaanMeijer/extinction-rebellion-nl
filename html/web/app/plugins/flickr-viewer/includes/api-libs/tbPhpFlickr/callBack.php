<?php
session_start();

    require_once 'Credentials.php';
    require_once("phpFlickr.php");
    
    $f = new phpFlickr(Credentials::API_KEY, Credentials::API_SECRET, true); 
    $f->getAccessToken(); 
    $OauthToken = $f->getOauthToken(); 
    $OauthSecretToken = $f->getOauthSecretToken(); 

    echo ""."\$OauthToken=".$OauthToken." \$OauthSecretToken".$OauthSecretToken."";
?>