<?php

namespace Mahopam\Jira;

use \Curl\Curl;

class Connector
{
   protected $user;
   protected $password;
   protected $baseUrl;

   public function __construct($baseUrl, $user, $password)
   {
      $this->baseUrl = $baseUrl;
      $this->user = $user;
      $this->password = $password;
   }

   public function get($relativeUrl)
   {
      $curl = new Curl();
      $curl->setBasicAuthentication($this->user, $this->password);
      $curl->get($this->baseUrl . $relativeUrl);

      if ($curl->error) {
         throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
      }

      return $curl->response;
   }
}
