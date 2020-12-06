<html>
<head>
        <meta charset="utf-8">
</head>

<?php

// Create an instance of the tbFlickr class
require_once 'tbPhpFlickr.php';

// Create an instance of the CacheManager class
require_once 'CacheManager.php';

// List the names of the first 50 sets
$tbPhpFlickr = new tbPhpFlickr( $myCache = new CacheManager(), false );

$myCache->enableCache( 'fs','/Users/iankennerley/Documents/sites/www.flickr.dev/cache' );

$tbPhpFlickr->photosets_getList(1, 50);

echo '<pre>';
print_r($tbPhpFlickr);
echo '</pre>';

$sets = $tbPhpFlickr->parsed_response['photosets']['photoset'];

echo '<pre>';
print_r($sets);
echo '</pre>';


foreach ($sets as $set) {
    echo $set['title']."<br />";
}

?>
</html>