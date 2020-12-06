<?php

require_once 'tbPhpFlickr.php';


$builder = new ServiceBuilder();
$service = $builder->provider(new TwitterApi())
->apiKey('7b6016016bf8eccff750fce908ab714e')
->apiSecret('5bd14fb9beb5ed43')
->build();


$requestToken = $service.getRequestToken();


echo '<pre>';
print_r($requestToken);
print_r($builder);
echo '</pre>';