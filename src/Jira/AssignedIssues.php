<?php 

namespace Mahopam\Atlassian\Jira;

use \Mahopam\Atlassian\Jira\Connector as JiraConnector;

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
      $relativeUrl = "search?jql=assignee=" . $this->userId;

      $response = $this->jiraConnector->get($relativeUrl);

      return $response;
   }
}
