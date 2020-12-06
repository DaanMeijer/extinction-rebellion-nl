# tbPhpFlickr

PhpFlickr + Cache + OAuth (scribe) 

This takes the [PhpFlickr](http://phpflickr.com/) classes developed by Dan Coulter and adds OAuth authentication using [Scribe-php](https://github.com/tbilou/scribe-php).

### Example
~~~
<?php

require_once 'tbPhpFlickr.php';

// List the names of the first 50 sets
$tbPhpFlickr = new tbPhpFlickr(false);
$tbPhpFlickr->photosets_getList(1, 50);
$sets = $tbPhpFlickr->parsed_response['photosets']['photoset'];

foreach ($sets as $set) {
    echo $set['title']."<br />";
}

?>
~~~