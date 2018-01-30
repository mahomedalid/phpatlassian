<?php

require __DIR __ . '/vendor/autoload.php';
include_once('config.php');

use Mahopam\Atlassian\Jira\DevStats;
use Mahopam\Atlassian\Jira\Connector;
use Mahopam\Atlassian\Stats\StorageCsv;

$connector = new Connector(MAHOPAM_JIRA_EXAMPLE_BASEURL, MAHOPAM_JIRA_EXAMPLE_USER, MAHOPAM_JIRA_EXAMPLE_PASSWD);
$storage = new StorageCsv();

$cleanCache = !empty($argv[1]);
$dev = new DevStats($connector, $storage);

$dev->storeWorkLogs($cleanCache);
$dev->storeSprints();
$dev->storeResolvedIssues();

exit;
