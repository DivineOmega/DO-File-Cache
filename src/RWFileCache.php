<?php

namespace rapidweb\RWFileCache;

class RWFileCache
{
    private $config = [
                            "unixLoadUpperThreshold" => 4.0,
                            "gzipCompression" => true,
                            "cacheDirectory" => "/tmp/rwFileCacheStorage/",
                            /*"garbageCollection" => [
                                "chanceToRun" => 0.05,
                                "maxAgeSeconds" => 2678400
                            ],*/
                            "fileExtension" => "cache"
                        ];
                        
    public function __construct()
    {
        
    }
    
    public function changeConfig($configArray)
    {
        if (!is_array($configArray)) {
            return false;
        }
        
        $this->config = array_merge($this->config, $configArray);
        
        return true;
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
        
        $filePath = $this->getFilePathFromKey($key);
        
        $result = file_put_contents($filePath, $cacheFileData);
        
        return ($result ? true : false);
    }
    
    public function get($key)
    {
        $filePath = $this->getFilePathFromKey($key);
        
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
        $filePath = $this->getFilePathFromKey($key);
        
        return unlink($filePath);
    }
    
    public function flush()
    {
        return $this->deleteDirectoryTree($this->config['cacheDirectory']);
    }
    
    public function deleteDirectoryTree($directory)
    {
        $filePaths = scandir($directory);
        
        foreach ($filePaths as $filePath) {
            
            if ($filePath=='.' || $filePath=='..') {
                continue;
            }
            
            $fullFilePath = $directory.'/'.$filePath;
            
            if(is_dir($fullFilePath)) {
                $result = $this->deleteDirectoryTree($fullFilePath);
                
                if ($result) {
                    $result = rmdir($fullFilePath);
                }
                
            } else {
                $result = unlink($fullFilePath);
            }
            
            if (!$result) {
                return false;
            }
        }
        
        return true;
    }
    
    public function increment($key, $offset = 1)
    {
        $filePath = $this->getFilePathFromKey($key);
        
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
        
        $content = $cacheObj->content;
            
        if ($unserializedContent = @unserialize($content)) {
            $content = $unserializedContent;
        }
        
        if (!$content || !is_numeric($content)) {
            return false;
        }
        
        $content += $offset;
        
        return $this->set($key, $content, $cacheObj->expiryTimestamp);
    }
    
    public function decrement($key, $offset = 1)
    {
        return $this->increment($key, -$offset);
    }
    
    public function replace($key, $content, $expiry = 0)
    {
        if (!$this->get($key)) {
            return false;
        }
        
        return $this->set($key, $content, $expiry);
    }
    
    private function getFilePathFromKey($key)
    {
        $key = basename($key);
        
        $badChars = array('-', '.', '_', '\\', '*', '\"', '?', '[', ']', ':', ';', '|', '=', ',');
        
        $key = str_replace($badChars, '/', $key);
        
        while(strpos($key, '//')!==false) {
            $key = str_replace('//', '/', $key);
        }
        
        $endOfDirectory = strrpos($key, '/');
        
        if ($endOfDirectory !== false) {
            
            $directoryToCreate = $this->config['cacheDirectory'].substr($key, 0, $endOfDirectory);
            
            if (!file_exists($directoryToCreate)) {
            
                $result = mkdir($directoryToCreate, 0777, true);
                
                if(!$result) {
                    return false;
                }
            }
        }
        
        $filePath = $this->config['cacheDirectory'].$key.'.'.$this->config['fileExtension'];
        
        return $filePath;
    }
}