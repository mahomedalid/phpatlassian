<?php

namespace Mahopam\Atlassian\Cache;

/**
 * Unsecure creepy simple filesystem cache
 * @package Mahopam\Atlassian\Cache
 */
class SimpleFileSystem implements ICache
{
    private $cacheDir = '/tmp';

    public function __construct($cacheDir = '/tmp')
    {
        $this->cacheDir = $cacheDir;
    }

    public function set($context, $key, $content)
    {
        $file = $this->cacheDir . DIRECTORY_SEPARATOR . $key . '.context';
        return file_put_contents($file, serialize($content));
    }

    public function get($context, $key)
    {
        $file = $this->cacheDir . DIRECTORY_SEPARATOR . $key . '.context';

        if (is_readable($file)) {
            return unserialize(file_get_contents($file));
        }
    }
}