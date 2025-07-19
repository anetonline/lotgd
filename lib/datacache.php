<?php

// Updated data caching library for LoGD
// This will always use the local "cache" directory for cache files.

$datacache = [];
define("DATACACHE_FILENAME_PREFIX", "cache_");

// Return the path to the cache directory, always relative to this file.
function get_datacache_dir(): string {
    return __DIR__ . '/cache';
}

function datacache(string $name, $duration = 60): bool|array
{
    global $datacache;
    $fileName = makecachetempname($name);
    if (isset($datacache[$name])) {
        return $datacache[$name];
    } 
    if (
        file_exists($fileName)
        && filemtime($fileName) > strtotime("-$duration seconds")
    ) {
        $file = @file_get_contents($fileName);
        if ($file === "" || $file === false) return false;
        $datacache[$name] = @unserialize($file);
        return $datacache[$name];
    }
    return false;
}

function updatedatacache(string $name, array $data): bool
{
    global $datacache;
    $fileName = makecachetempname($name);
    $datacache[$name] = $data;
    $dir = dirname($fileName);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $file = @fopen($fileName, 'w');
    if (!$file) return false;
    $written = fwrite($file, serialize($data));
    fclose($file);
    return $written !== false;
}

function invalidatedatacache(string $name): bool
{
    global $datacache;
    $fileName = makecachetempname($name);
    if (!file_exists($fileName)) return false;
    @unlink($fileName);
    unset($datacache[$name]);
    return true;
}

// Invalidates *all* caches, which contain $name at the beginning of their filename.
function massinvalidate(string $name): bool
{
    $path = get_datacache_dir();
    if (!is_dir($path)) return false;
    $dir = @dir($path);
    if (!$dir) return false;
    while(false !== ($file = $dir->read())) {
        if (strpos($file, $name) !== false && strpos($file, DATACACHE_FILENAME_PREFIX) === 0) {
            @unlink($path . '/' . $file);
        }
    }
    $dir->close();
    return true;
}

function makecachetempname(string $name): string
{
    $path = get_datacache_dir();
    $name = preg_replace("'[^A-Za-z0-9.-]'", "", $name);
    $name = DATACACHE_FILENAME_PREFIX . $name;
    $filePath = "$path/$name";
    // Normalize slashes
    $filePath = preg_replace("'//+'","/", $filePath);
    $filePath = preg_replace("'\\\\'","\\", $filePath);
    return $filePath;
}
