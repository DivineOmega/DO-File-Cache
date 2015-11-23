# RW File Cache

[![StyleCI](https://styleci.io/repos/46275735/shield)](https://styleci.io/repos/46275735)

RW File Cache is a PHP File-based Caching Library.

Its syntax is designed to closely resemble the PHP `memcache` extension.

## Installation

You can easily install with composer. Just add the following to your `composer.json` then run `composer install`.

```json
{
  "require": {
       "rapidwebltd/rw-file-cache": "dev-master"
   }
}
```

## Usage

This section will show you how to use RW File Cache. If you have used `memcache` before, this should be pretty familiar.

### Setup & Configuration

Before you can do anything with RW File Cache, you must instatiate it and then, if you wish, set some configuration options.

```php
require_once "vendor/autoload.php";

$cache = new \rapidweb\RWFileCache\RWFileCache();

$cache->changeConfig(array("cacheDirectory" => "/tmp/rwFileCacheStorage/"));
```

This code creates a new RW File Cache object called `$cache` and then configures it to store its cache files in the `/tmp/rwFileCacheStorage/` directory.

There are several different configuration variables you can override. The table below describes them.

| Option        | Description           | Default  |
| ------------- |:-------------:| -----:|
| `cacheDirectory` | The directory in which you wish the cache files to be stored. We recommend you change this to a site-specific directory and ensure it is outside of the web root. You must include a trailing slash. | `/tmp/rwFileCacheStorage/` |
| `gzipCompression` | Whether or not to compress cache files using gzip. Unless you are storing very small values in your cache files, we recommend you leave this enabled. | `true` |
| `fileExtension` | The file extension that will be appended to all your cache files. | `cache` |
| `unixLoadUpperThreshold` | If your server's load is greater than this value, cache files will be returned regardless of whether they have expired. This can be used to prevent cache files being regenerated when server load is high. If you do not wish to use this feature, set this option to an obscenely high value. | `4.00` |

### Setting a cache item

Putting something in your file cache is easy. You just need to use the `set` method, as shown below.

```php
$cache->set('nursery_rhyme',"Mary had a little lamb", strtotime('+ 1 day'));
```

The first parameter is the cache key, which uniquely references this cache item. 

The second parameter is the cache value - what you wish to store in this cache item. This can be a string, integer, array, object or any type of serializable PHP variable.

The third paramter is the expiry time. It can be specified as a UNIX timestamp or as a number of seconds less than 30 days worth. Cache items will expire and not be retrievable when this time is reached.

Note that if you use dots, dashes, underscores or a few other special characters in your cache key, the created cache files will be put into a directory structure. For example, a cache key of `objects.cars.redCar` will be stored in `objects/cars/redCar.cache`. This is useful if you wish to categorise cache files and to prevent too many cache files building up in a single directory.

### Getting a cache item

To get a cache item you've previously stored, you need to use the `get` method. An example of how to do this is shown below.

```php
$var = $cache1->get('nursery_rhyme');
```

The only parameter is the cache key you defined when setting the cache item. You can retrieve any cached variable in this way.

### Other RW File Cache methods

The setting and retrieval of cache items are the most important parts of RW File Cache. In fact, the `set` and `get` methods are probably all you will need.

However, the library provides the following more advanced commands if you need them.

* `$cache->delete($key)` - Delete a specific item from the cache.
* `$cache->flush()` - Deletes all items from the cache.
* `$cache->replace($key, $content, $expiry)` - Similar to the `set` method, but will only update a cache item's value if the cache item already exists.
* `$cache->increment($key, $offset)` - Increment a numeric cache item value by the specified offset (or one if the offset is ommited).
* `$cache->decrement($key, $offset)` - Decrements a numeric cache item value by the specified offset (or one if the offset is ommited).
