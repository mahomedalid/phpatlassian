<?php

namespace Mahopam\Atlassian\Stats;

interface IStorage
{
    public function storeSprint(array $rawData);
    public function getIssue($id);
    public function storeIssue(array $rawData);
}