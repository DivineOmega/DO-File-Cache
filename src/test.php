<?php

require_once "RWFileCache.php";

$cache1 = new \rapidweb\RWFileCache\RWFileCache();

$cache1->changeConfig(array("cacheDirectory" => "/tmp/rwFileCacheStorage/"));

$cache1->set('first.test.stringContent', "Mary had a little lamb.", strtotime('+ 1 day'));

$var = $cache1->get('first.test.stringContent');

var_dump($var);
