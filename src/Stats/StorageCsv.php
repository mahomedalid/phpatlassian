<?php
/**
 * Created by PhpStorm.
 * User: mpacheco
 * Date: 1/30/18
 * Time: 1:27 PM
 */

namespace Mahopam\Atlassian\Stats;

class StorageCsv implements IStorage
{
    private $dir;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function storeSprint(array $rawData)
    {

    }

    public function getIssue($id)
    {

    }

    public function storeIssue(array $rawData)
    {

    }
}