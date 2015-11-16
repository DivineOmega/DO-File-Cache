<?php

namespace rapidweb\RWFileCache;

class RWFileCache
{
    private $config = [
                            "unixLoadUpperThreshold" => 4.0,
                            "gzipCompression" => true,
                            "cacheDirectory" => "/tmp/rwFileCacheStorage/",
                            "garbageCollection" => [
                                "chanceToRun" => 0.05,
                                "maxAgeSeconds" => 2678400
                            ],
                            "fileExtension" => "cache"
                        ];
                        
    public function __construct()
    {
        
    }
    
    public function set($key, $content, $expiry = 0)
    {
        $cacheObj = new \stdClass();
        
        if (!is_string($content)) {
            $content = serialize($content);
        }
        
        $cacheObj->content = $content;
        
        if (!$expiry){
            
            // If no expiry specified, set to 'Never' expire timestamp (+10 years)
            $cacheObj->expiryTimestamp = time() + 315360000;
            
        } else if ($expiry>2592000) {
            
            // For value greater than 30 days, interpret as timestamp
            $cacheObj->expiryTimestamp = $expiry;
            
        } else { 
            
            // Else, interpret as number of seconds
            $cacheObj->expiryTimestamp = time() + $expiry;
            
        }
        
        $cacheFileData = json_encode($cacheObj);
        
        if ($this->config['gzipCompression']) {
            $cacheFileData = gzcompress($cacheFileData);
        }
        
        $filePath = $this->config['cacheDirectory'].$key.'.'.$this->config['fileExtension'];
        
        $result = file_put_contents($filePath, $cacheFileData);
        
        return ($result ? true : false);
    }
    
    public function get($key)
    {
        $filePath = $this->config['cacheDirectory'].$key.'.'.$this->config['fileExtension'];
        
        if (!file_exists($filePath)) {
            return false;
        }
        
        if (!is_readable($filePath)) {
            return false;
        }
        
        $cacheFileData = file_get_contents($filePath);
        
        if ($this->config['gzipCompression']) {
            $cacheFileData = gzuncompress($cacheFileData);
        }
        
        $cacheObj = json_decode($cacheFileData);
        
        $unixLoad = sys_getloadavg();
        
        if ($cacheObj->expiryTimestamp > time() || $unixLoad[0] >= $this->config['unixLoadUpperThreshold']) {
            
            // Cache item has not yet expired or system load is too high
            
            $content = $cacheObj->content;
            
            if ($unserializedContent = @unserialize($content)) {
                $content = $unserializedContent;
            }
            
            return $content;
            
        } else {
        
            // Cache item has expired
            
            return false;
        
        }
    }
    
    public function delete($key)
    {
        $filePath = $this->config['cacheDirectory'].$key.'.'.$this->config['fileExtension'];
        
        return unlink($filePath);
    }
}