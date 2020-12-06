<?php
/* phpFlickr Class 3.1
 * Written by Dan Coulter (dan@dancoulter.com)
 * Project Home Page: http://phpflickr.com/
 * Released under GNU Lesser General Public License (http://www.gnu.org/copyleft/lgpl.html)
 * For more information about the class and upcoming tools and toys using it,
 * visit http://www.phpflickr.com/
 *
 *	 For installation instructions, open the README.txt file packaged with this
 *	 class. If you don't have a copy, you can see it at:
 *	 http://www.phpflickr.com/README.txt
 *
 *	 Please submit all problems or questions to the Help Forum on my Google Code project page:
 *	 http://code.google.com/p/phpflickr/issues/list
 * 
 *   Authentification Oauth added by DantSu
 *   http://www.asociaux.fr - http://www.dantsu.com
 *
 */ 
if ( !class_exists('phpFlickr') ) {
    if (session_id() == "") {
        @session_start();
    }

    class phpFlickr {
        var $api_key;
        var $secret;
        
        var $rest_endpoint = 'https://api.flickr.com/services/rest/';
        var $upload_endpoint = 'https://api.flickr.com/services/upload/';
        var $replace_endpoint = 'https://api.flickr.com/services/replace/';
        var $oauthrequest_endpoint = 'https://www.flickr.com/services/oauth/request_token/';
        var $oauthauthorize_endpoint = 'https://www.flickr.com/services/oauth/authorize/';
        var $oauthaccesstoken_endpoint = 'https://www.flickr.com/services/oauth/access_token/';
        var $req;
        var $response;
        var $parsed_response;
        var $last_request = null;
        var $die_on_error;
        var $error_code;
        Var $error_msg;
        var $oauth_token;
        var $oauth_secret;
        var $php_version;
        var $custom_post = null;


        function phpFlickr ($api_key, $secret = NULL, $die_on_error = false) {
            //The API Key must be set before any calls can be made.  You can
            //get your own at http://www.flickr.com/services/api/misc.api_keys.html
            $this->api_key = $api_key;
            $this->secret = $secret;
            $this->die_on_error = $die_on_error;
            $this->service = "flickr";

            //Find the PHP version and store it for future reference
            $this->php_version = explode("-", phpversion());
            $this->php_version = explode(".", $this->php_version[0]);
        }
/*
        function setCustomPost ( $function ) {
            $this->custom_post = $function;
        }
  */

        function post ($data, $url='') {
        
            if($url == '')
            $url = $this->rest_endpoint;
            
            //if ( !preg_match("|http://(.*?)(/.*)|", $url, $matches) ) {
            // IK changed...
            if ( !preg_match("|https://(.*?)(/.*)|", $url, $matches) ) {
                die('There was some problem figuring out your endpoint');
            }
            
            if ( function_exists('curl_init') ) {
                // Has curl. Use it!
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);
                curl_close($curl);
            } else {
                // Use sockets.
                foreach ( $data as $key => $value ) {
                    $data[$key] = $key . '=' . urlencode($value);
                }
                
                $data = implode('&', $data);
                
                $fp = @pfsockopen($matches[1], 80);
                if (!$fp) {
                    die('Could not connect to the web service');
                }
                fputs ($fp,'POST ' . $matches[2] . " HTTP/1.1\n");
                fputs ($fp,'Host: ' . $matches[1] . "\n");
                fputs ($fp,"Content-type: application/x-www-form-urlencoded\n");
                fputs ($fp,"Content-length: ".strlen($data)."\n");
                fputs ($fp,"Connection: close\r\n\r\n");
                fputs ($fp,$data . "\n\n");
                $response = "";
                while(!feof($fp)) {
                    $response .= fgets($fp, 1024);
                }
                fclose ($fp);
                
                $chunked = false;
                $http_status = trim(substr($response, 0, strpos($response, "\n")));
                if ( $http_status != 'HTTP/1.1 200 OK' ) {
                    die('The web service endpoint returned a "' . $http_status . '" response');
                }
                if ( strpos($response, 'Transfer-Encoding: chunked') !== false ) {
                    $temp = trim(strstr($response, "\r\n\r\n"));
                    $response = '';
                    $length = trim(substr($temp, 0, strpos($temp, "\r")));
                    while ( trim($temp) != "0" && ($length = trim(substr($temp, 0, strpos($temp, "\r")))) != "0" ) {
                        $response .= trim(substr($temp, strlen($length)+2, hexdec($length)));
                        $temp = trim(substr($temp, strlen($length) + 2 + hexdec($length)));
                    }
                } elseif ( strpos($response, 'HTTP/1.1 200 OK') !== false ) {
                    $response = trim(strstr($response, "\r\n\r\n"));
                }
            }
            return $response;
        }
        
        function request ($command, $args = array())
        {
            //Sends a request to Flickr's REST endpoint via POST.
            if (substr($command,0,7) != "flickr.") {
                $command = "flickr." . $command;
            }

            //Process arguments, including method and login data.
            $args = array_merge(array("method" => $command, "format" => "php_serial", "api_key" => $this->api_key), $args);
            ksort($args);
            $auth_sig = "";
            $this->last_request = $args;
            
            foreach ($args as $key => $data) {
                if ( is_null($data) ) {
                    unset($args[$key]);
                    continue;
                }
                $auth_sig .= $key . $data;
            }
            if (!empty($this->secret)) {
                $api_sig = md5($this->secret . $auth_sig);
                $args['api_sig'] = $api_sig;
            }
            
            if(!$args = $this->getArgOauth($this->rest_endpoint, $args))
            return false;
            
            $this->response = $this->post($args);
            
            /*
             * Uncomment this line (and comment out the next one) if you're doing large queries
             * and you're concerned about time.  This will, however, change the structure of
             * the result, so be sure that you look at the results.
             */
            $this->parsed_response = $this->clean_text_nodes(unserialize($this->response));
            if ($this->parsed_response['stat'] == 'fail') {
                if ($this->die_on_error) die("The Flickr API returned the following error: #{$this->parsed_response['code']} - {$this->parsed_response['message']}");
                else {
                    $this->error_code = $this->parsed_response['code'];
                    $this->error_msg = $this->parsed_response['message'];
                    $this->parsed_response = false;
                }
            } else {
                $this->error_code = false;
                $this->error_msg = false;
            }
            return $this->response;
        }

        function clean_text_nodes ($arr) {
            if (!is_array($arr)) {
                return $arr;
            } elseif (count($arr) == 0) {
                return $arr;
            } elseif (count($arr) == 1 && array_key_exists('_content', $arr)) {
                return $arr['_content'];
            } else {
                foreach ($arr as $key => $element) {
                    $arr[$key] = $this->clean_text_nodes($element);
                }
                return($arr);
            }
        }

        function getArgOauth($url, $data) {
            if(!empty($this->oauth_token) && !empty($this->oauth_secret))
            {
                $data['oauth_consumer_key'] = $this->api_key;
                $data['oauth_timestamp'] = time();
                $data['oauth_nonce'] = md5(uniqid(rand(), true));
                $data['oauth_signature_method'] = "HMAC-SHA1";
                $data['oauth_version'] = "1.0";
                $data['oauth_token'] = $this->oauth_token;
                
                if(!$data['oauth_signature'] = $this->getOauthSignature($url, $data))
                return false;
            }
            return $data;
        }
        
        function requestOauthToken() {
            if (session_id() == '')
            session_start();
            
            if(!isset($_SESSION['oauth_tokentmp']) || !isset($_SESSION['oauth_secrettmp']) || 
            $_SESSION['oauth_tokentmp'] == '' ||  $_SESSION['oauth_secrettmp'] == '')
            {
                $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI'];
                $this->getRequestToken($callback);
                return false;
            }
            else
            return $this->getAccessToken();
        }
        
        function getRequestToken($callback) {
            if (session_id() == '')
            session_start();
            
            $data = array(
                'oauth_consumer_key' => $this->api_key,
                'oauth_timestamp' => time(),
                'oauth_nonce' => md5(uniqid(rand(), true)),
                'oauth_signature_method' => "HMAC-SHA1",
                'oauth_version' => "1.0",
                'oauth_callback' => $callback
            );
            
            if(!$data['oauth_signature'] = $this->getOauthSignature($this->oauthrequest_endpoint, $data))
            return false;
            
            $reponse = $this->oauthResponse($this->post($data, $this->oauthrequest_endpoint));
            
            if(!isset($reponse['oauth_callback_confirmed']) || $reponse['oauth_callback_confirmed'] != 'true')
            {
                $this->error_code = 'Oauth';
                //$this->error_msg = display_array($reponse);
                // IK added print_r...
                $this->error_msg = print_r($reponse);
                return false;
            }
            
            
            $_SESSION['oauth_tokentmp'] = $reponse['oauth_token'];
            $_SESSION['oauth_secrettmp'] = $reponse['oauth_token_secret'];
            
            //header("location: ".$this->oauthauthorize_endpoint.'?oauth_token='.$reponse['oauth_token']);
            // IK added optional perms param...
            header("location: ".$this->oauthauthorize_endpoint.'?oauth_token='.$reponse['oauth_token'] . '&perms=write');
            
            $this->error_code = '';
            $this->error_msg = '';
            return true;
        }
        
        function getAccessToken() {
            if (session_id() == '')
            session_start();
            
            $this->oauth_token = $_SESSION['oauth_tokentmp'];
            $this->oauth_secret = $_SESSION['oauth_secrettmp'];
            unset($_SESSION['oauth_tokentmp']);
            unset($_SESSION['oauth_secrettmp']);
            
            if(!isset($_GET['oauth_verifier']) || $_GET['oauth_verifier'] == '')
            {
                $this->error_code = 'Oauth';
                $this->error_msg = 'oauth_verifier is undefined.';
                return false;
            }
            
            $data = array(
                'oauth_consumer_key' => $this->api_key,
                'oauth_timestamp' => time(),
                'oauth_nonce' => md5(uniqid(rand(), true)),
                'oauth_signature_method' => "HMAC-SHA1",
                'oauth_version' => "1.0",
                'oauth_token' => $this->oauth_token,
                'oauth_verifier' => $_GET['oauth_verifier']
            );
            
            if(!$data['oauth_signature'] = $this->getOauthSignature($this->oauthaccesstoken_endpoint, $data))
            return false;
            
            $reponse = $this->oauthResponse($this->post($data, $this->oauthaccesstoken_endpoint));

            if(isset($reponse['oauth_problem']) && $reponse['oauth_problem'] != '')
            {
                $this->error_code = 'Oauth';
                $this->error_msg = display_array($reponse);
                return false;
            }
            
            $this->oauth_token = $reponse['oauth_token'];
            $this->oauth_secret = $reponse['oauth_token_secret'];
            $this->error_code = '';
            $this->error_msg = '';
            return true;
        }
        
        function getOauthSignature($url, $data) {
            if($this->secret == '')
            {
                $this->error_code = 'Oauth';
                $this->error_msg = 'API Secret is undefined.';
                return false;
            }
            
            ksort($data);
            
            $adresse = 'POST&'.rawurlencode($url).'&';
            $param = '';
            foreach ( $data as $key => $value )
            $param .= $key.'='.rawurlencode($value).'&';
            $param = substr($param, 0, -1);
            $adresse .= rawurlencode($param);
            
            return base64_encode(hash_hmac('sha1', $adresse, $this->secret.'&'.$this->oauth_secret, true));
        }

        function oauthResponse($response) {
            $expResponse = explode('&', $response);
            $retour = array();
            foreach($expResponse as $v)
            {
                $expArg = explode('=', $v);
                $retour[$expArg[0]] = $expArg[1];
            }
            return $retour;   
        }
        
        function setOauthToken ($token, $secret) {
            $this->oauth_token = $token;
            $this->oauth_secret = $secret;
        }
        function getOauthToken () {
            return $this->oauth_token;
        }
        function getOauthSecretToken () {
            return $this->oauth_secret;
        }
        
    }
}


?>