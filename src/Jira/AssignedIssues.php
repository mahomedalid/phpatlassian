<?php 

namespace Mahopam\Jira;

use \Mahopam\Jira\Connector as JiraConnector;

class AssignedIssues
{
   protected $userId;
   protected $jiraConnector;

   public function __construct(JiraConnector $jiraConnector, $userId)
   {
      $this->jiraConnector = $jiraConnector;
      $this->userId = $userId;
   }

   public function getIssues($clear_cache = false)
   {
      $file = "cache/" . date("Ymd") . md5($this->userId) . ".issues";

      if (is_readable($file) && !$clear_cache) {
         return unserialize(file_get_contents($file));
      }

      $relativeUrl = "search?jql=assignee=" . $this->userId;

      $response = $this->jiraConnector->get($relativeUrl);
      file_put_contents($file, serialize($response));
      return $response;
   }
}
