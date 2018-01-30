<?php

namespace Mahopam\Atlassian\Jira;

use \Curl\Curl;
use \Mahopam\Atlassian\Cache\ICache;

class Connector
{
    protected $user;
    protected $password;
    protected $baseUrl;
    /**
     * @var Cache
     */
    protected $cache;

    public function __construct($baseUrl, $user, $password)
    {
        $this->baseUrl = $baseUrl;
        $this->user = $user;
        $this->password = $password;
    }

    public function setCacheInstance(ICache $cache)
    {
        $this->cache = $cache;
    }

    public function getFromCache($context)
    {
        if (empty($this->cache)) {
            return;
        }

        return $this->cache->get($context, date('Ymd'));
    }

    public function setToCache($context, $content)
    {
        return $this->cache->set($context, date('Ymd'), $content);
    }

    public function get($relativeUrl)
    {
        $context = sha1($relativeUrl);
        if ($cache = $this->getFromCache($context)) {
            return $cache;
        }
        $curl = new Curl();
        $curl->setBasicAuthentication($this->user, $this->password);
        $curl->get($this->baseUrl . $relativeUrl);

        if ($curl->error) {
            throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        }

        $this->setToCache($context, $curl->response);

        return $curl->response;
    }
}