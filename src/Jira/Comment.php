<?php

namespace Mahopam\Jira;

class Comment
{
    protected $rawComment;
    protected $pullRequest;

    public function __construct(\StdClass $comment)
    {
        $this->rawComment = $comment->comment;
        $this->pullRequest = $comment->pull_request;
    }

    public function __toString ()
    {
        return (string)implode("\t", [
            $this->rawComment->links->html->href,
            $this->rawComment->content->raw,
            $this->rawComment->user->display_name,
            $this->rawComment->user->username,
            $this->rawComment->created_on
        ]);
    }

    public function toArray ()
    {
        return [
            'href' => $this->rawComment->links->html->href,
            'comment' => str_replace(["\n", "\t"], " ", $this->rawComment->content->raw),
            'comment_html' => $this->rawComment->content->html,
            'user' => $this->rawComment->user->display_name,
            'username' => $this->rawComment->user->username,
            'created_on' => $this->rawComment->created_on
        ];
    }
}