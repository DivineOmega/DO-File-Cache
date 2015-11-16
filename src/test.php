<?php

require_once "RWFileCache.php";

$cache1 = new \rapidweb\RWFileCache\RWFileCache();

$cache1->changeConfig(array("cacheDirectory" => "/tmp/rwFileCacheStorage/"));

$cache1->set('test',50, strtotime('+ 1 day'));

$var = $cache1->get('test');

var_dump($var);

$cache1->replace('test',"mary had a little lamb", strtotime('+ 1 day'));

$var = $cache1->get('test');

var_dump($var);