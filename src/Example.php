<?php

require_once 'RWFileCache.php';

$cache1 = new \rapidweb\RWFileCache\RWFileCache();

$cache1->changeConfig(['cacheDirectory' => '/tmp/rwFileCacheStorage/']);

// Test basic string storage and retrieval
$key = 'first.test.stringContent';
$cache1->set($key, 'Mary had a little lamb.', strtotime('+ 1 day'));
$var = $cache1->get($key);
var_dump($var);

// Test empty array storage and retrieval
$key = 'second.test.emptyArray';
$cache1->set($key, [], strtotime('+ 1 day'));
$var = $cache1->get($key);
var_dump($var);

// Test numeric zero storage and retrieval
$key = 'third.test.numericZero';
$cache1->set($key, 0, strtotime('+ 1 day'));
$var = $cache1->get($key);
var_dump($var);

// Test boolean false storage and retrieval
$key = 'fourth.test.booleanFalse';
$cache1->set($key, false, strtotime('+ 1 day'));
$var = $cache1->get($key);
var_dump($var);

// Test boolean true storage and retrieval
$key = 'fifth.test.booleanTrue';
$cache1->set($key, true, strtotime('+ 1 day'));
$var = $cache1->get($key);
var_dump($var);
