<?php

require_once "RWFileCache.php";

$cache1 = new \rapidweb\RWFileCache\RWFileCache();

//$cache1->set('test', 'Mary had a little lamb.', strtotime('+5 second'));

$var = $cache1->get('test');

var_dump($var);
