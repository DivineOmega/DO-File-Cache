# RW File Cache

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

