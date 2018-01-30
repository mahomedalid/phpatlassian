<?php

namespace Mahopam\Atlassian\Jira;

class Response
{
    protected $rawResponse;
    protected $values = [];
    protected $comments;
    const COMMENT_INDEX = 'comment';

    public function __construct(\StdClass $response = null)
    {
        if (!empty($response)) {
            $this->_processResponse($response);
        }
    }

    public function addResponse(\StdClass $response)
    {
        $this->_processResponse($response);
    }

    protected function _processResponse($rawResponse)
    {
        $this->rawResponse [] = $rawResponse;
        $values = $rawResponse->values;
        foreach ($values as $value) {
            $vars = get_object_vars($value);
            $type = key($vars);
            if (!isset($this->values[$type])) {
                $this->values[$type] = [];
            }
            $this->values[$type][] = $value;
        }
    }

    public function getComments()
    {
        return $this->values[static::COMMENT_INDEX];
    }

    public function __toString()
    {
        return (string)var_export($this->values, true);
    }
}