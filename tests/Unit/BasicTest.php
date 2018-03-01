<?php

use PHPUnit\Framework\TestCase;

final class BasicTests extends TestCase
{
    private $cache = null;

    public function setUp()
    {
        $this->cache = new \rapidweb\RWFileCache\RWFileCache();
    }

    public function testSettingInvalidCacheConfig()
    {
        $this->assertFalse($this->cache->changeConfig('invalid_data'));
    }

    public function testDelete()
    {
        $stored = 'Mary had a little lamb.';

        $key = __FUNCTION__;
        $this->cache->set($key, $stored, strtotime('+ 1 day'));

        $this->assertEquals($stored, $this->cache->get($key));

        $this->cache->delete($key);

        $this->assertFalse($this->cache->get($key));
    }

    public function testFlush()
    {
        $stored = 'Mary had a little lamb.';

        $key = __FUNCTION__;
        $this->cache->set($key, $stored, strtotime('+ 1 day'));

        $this->assertEquals($stored, $this->cache->get($key));

        $this->cache->flush($key);

        $this->assertFalse($this->cache->get($key));
    }

    public function testIncrementAndDecrement()
    {
        $stored = 1;

        $key = __FUNCTION__;
        $this->cache->set($key, $stored, strtotime('+ 1 day'));

        $retrieved = $this->cache->get($key);

        $this->assertEquals($stored, $retrieved);

        $this->cache->increment($key);

        $this->assertEquals(2, $this->cache->get($key));

        $this->cache->increment($key, 5);

        $this->assertEquals(7, $this->cache->get($key));

        $this->cache->decrement($key);

        $this->assertEquals(6, $this->cache->get($key));

        $this->cache->decrement($key, 10);

        $this->assertEquals(-4, $this->cache->get($key));
    }

    public function testReplace()
    {
        $stored = 'Mary had a little lamb.';
        $stored2 = 'Mary had a big dog.';

        $key = __FUNCTION__;

        $this->cache->replace($key, $stored, strtotime('+ 1 day'));

        $this->assertFalse($this->cache->get($key));

        $this->cache->set($key, $stored, strtotime('+ 1 day'));

        $this->assertEquals($stored, $this->cache->get($key));

        $this->cache->replace($key, $stored2, strtotime('+ 1 day'));

        $this->assertEquals($stored2, $this->cache->get($key));
    }

    public function testSetCacheThatHasAlreadyExpired()
    {
        $stored = 'Mary had a little lamb.';

        $key = __FUNCTION__;
        $this->cache->set($key, $stored, strtotime('- 1 day'));

        $this->assertFalse($this->cache->get($key));
    }

    public function testSetCacheWithoutExpiryTime()
    {
        $stored = 'Mary had a little lamb.';

        $key = __FUNCTION__;
        $this->cache->set($key, $stored);

        $this->assertEquals($stored, $this->cache->get($key));
    }

    public function testGetNonexistantCache()
    {
        $key = __FUNCTION__;
        $this->cache->get($key);

        $this->assertFalse($this->cache->get($key));
    }

    public function testIncrementNonExistantCache()
    {
        $key = __FUNCTION__;
        $this->assertFalse($this->cache->increment($key));
    }

    public function testDecrementNonExistantCache()
    {
        $key = __FUNCTION__;
        $this->assertFalse($this->cache->decrement($key));
    }

    public function testSetExpiryInSeconds()
    {
        $key = __FUNCTION__;
        $this->assertTrue($this->cache->set($key, 'test', 1));
    }

    public function testExpiryOfOneSecondCache()
    {
        $stored = 'expiry_test_value';

        $key = __FUNCTION__;
        $this->cache->set($key, 'expiry_test_value', 1);

        $this->assertEquals($stored, $this->cache->get($key));

        sleep(1);

        $this->assertFalse($this->cache->get($key));
    }

}