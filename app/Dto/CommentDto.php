<?php

namespace App\Dto;

class CommentDto
{
    public $id;
    public $content;
    public $parent_id;
    public $user_id;
    public $user_name;
    public $commented_comment_id;
    public $commented_comment_content;
    public $created_at;
    public $level;
    public $children;
    public $files;

    public function __construct($data)
    {
        $this->id = $data["id"];
        $this->content = $data["content"];
        $this->parent_id = $data["parent_id"];
        $this->user_id = $data["user_id"];
        $this->user_name = $data["user"]["name"];
        $this->commented_comment_id = $data["commented_comment_id"];
        $this->setCommentedCommentContent($data["commented_comment_content"]);
        $this->level = $data["level"];
        $this->created_at = $data["created_at"];
        $this->files = $data['files'];
        $this->children = $data["children"];
    }

    public function setCommentedCommentContent($value)
    {
        $this->commented_comment_content = (strlen($value) > 30) ? substr($value, 0, 30) . '...' : $value;

    }

    public function add($item)
    {
        $this->children[] = $item;
    }
}
