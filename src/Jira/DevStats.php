<?php

namespace Mahopam\Atlassian\Jira;

use Mahopam\Atlassian\Stats\IStorage;

class DevStats
{
    /**
     * @var Storage
     */
    private $storage;
    private $jiraConnector;
    protected $period = '-50 days';
    protected $additionalFilters = '';
    protected $additionalFields = '';
    protected $sprintsBoard = 'board/36/sprint?startAt=45';

    public function __construct(JiraConnector $jiraConnector, IStorage $storage, $userId)
    {
        $this->jiraConnector = $jiraConnector;
        $this->userId = $userId;
        $this->storage = $storage;
    }

    public function setJqlFilter($filter)
    {
        $this->additionalFilters = $filter;
    }

    public function setAdditionalFields($fields)
    {
        $this->additionalFields = $fields;
    }

    public function getWorkLogs($cleanCache = false)
    {
        $date = date('Y-m-d', strtotime($this->period));
        $relativeUrl = "search?jql=".urlencode("updated > {$date} and {$this->additionalFilters} and timespent > 0")."&fields=summary,worklog&maxResults=1000";

        $response = $this->jiraConnector->get($relativeUrl);

        return $response;
    }

    public function getCompletedTasks()
    {
        $date =  date('Y-m-d', strtotime($this->period));

        $relativeUrl = "search?jql=".urlencode("status in (Resolved) and updated > {$date} and {$this->additionalFilters} and timespent > 0")."&fields=summary,timeoriginalestimate,assignee,issuetype,parent,resolutiondate,{$this->additionalFields}&maxResults=1000";
        $response = $this->jiraConnector->get($relativeUrl);

        return $response;
    }

    public function getSprints()
    {
        $relativeUrl = $this->sprintsBoard;

        $response = $this->jiraConnector->get($relativeUrl);

        return $response;
    }

    public function storeSprints()
    {
        $sprints = $this->getSprints();

        foreach ($sprints->values as $sprint) {
            $this->storage->storeSprint($sprint);
        }
    }

    public function getIssueFromJira ($id)
    {
        $relativeUrl = "issue/{$id}";
        return $this->jiraConnector->get($relativeUrl);
    }

    public function calculateStoryPoints ($issue)
    {
        $key = $issue->key;
        $estimate = $issue->fields->timeoriginalestimate;
        if (!isset($issue->fields->parent) || !$issue->fields->parent) {
            return false;
        }
        $parentKey = $issue->fields->parent->key;
        $parentId = $issue->fields->parent->id;

        if ($parent = $this->storage->getIssue($parentKey)) {
        } else {
            $parentIssue = $this->getIssueFromJira($parentId);
            $success = $this->storeIssue(
                [
                    'key' => $parentIssue->key,
                    'name' => $parentIssue->fields->issuetype->name,
                    'originalEstimate' => $parentIssue->fields->aggregatetimeoriginalestimate ? $parentIssue->fields->aggregatetimeoriginalestimate : 0,
                    'assignee' => $parentIssue->fields->assignee ? $parentIssue->fields->assignee->key : '',
                    'parentStoryPoints' => $parentIssue->fields->customfield_10105,
                    'summary' => $parentIssue->fields->summary
                ]
            );
            $parent = $this->storage->getIssue($parentKey);
        }

        if ($parent['StoryPoints'] < 1) {
            return false;
        }

        #echo PHP_EOL, "SP: ", $parent['StoryPoints'];

        if (!$estimate && !$parent['OriginalEstimate']) {
            #echo PHP_EOL, "Reason 1";
            return false;
        }

        $estimate = $parent['OriginalEstimate'];

        if (!$parent['OriginalEstimate'] > 0 || !$parent['StoryPoints'])  {
            #echo PHP_EOL, "Reason 2";
            return false;
        }

        #echo PHP_EOL;
        #echo ("DIV: " . $estimate/$parent['OriginalEstimate'] . " EST: " . $estimate . " POG: " .  $parent['OriginalEstimate']);
        $storyPoints = ($estimate / $parent['OriginalEstimate'])*$parent['StoryPoints'];

        if($storyPoints > $parent['StoryPoints']) {
            #echo PHP_EOL, "Reason 3";
            return false;
        }

        return round($storyPoints) ? round($storyPoints) : 1;
    }



    public function storeResolvedIssues()
    {

    }

    public function updateStoryPoints()
    {

    }

    public function storeWorkLogs($cleanCache = false)
    {
        $worklogs = $this->getWorkLogs($cleanCache);
        foreach($worklogs->issues as $issue) {
            foreach($issue->fields->worklog->worklogs as $log) {
                if (!isset($log->author->key)) {
                    $dev = $log->author->name;
                } else {
                    $dev = $log->author->key;
                }
                $date = $log->started;
                $value = $log->timeSpentSeconds;
                $repo = $issue->key;
                $stat = "logged time";
                #$this->placeIntoDb($repo, $value, $stat, $date, $dev);
            }
        }
    }
}