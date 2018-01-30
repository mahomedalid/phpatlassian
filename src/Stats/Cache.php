<?php
/**
 * Created by PhpStorm.
 * User: mpacheco
 * Date: 1/30/18
 * Time: 12:53 PM
 */

namespace Mahopam\Stats;

interface Cache
{
    public function set($context, $key, $content);
    public function get($context, $key);
}