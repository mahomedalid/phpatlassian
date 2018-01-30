<?php 

namespace Mahopam\Atlassian\Slack;
use \Curl\Curl;

class Webhook
{
   protected $hookUrl;

   public function __construct($hookUrl)
   {
      $this->hookUrl = $hookUrl;
   }

   public function postMsg($options)
   {
      $curl = new Curl();
      return $curl->post($this->hookUrl, ["payload" => json_encode($options)]);
   }
}
