<?php

namespace Mahomap\Stats;

interface Storage
{
    public function storeSprint(array $rawData);
    public function getIssue($id);
    public function storeIssue(array $rawData);
}