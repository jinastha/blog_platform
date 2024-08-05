<?php

namespace App\Repo\Eloquent;

use App\Models\Comment;
use App\Repo\Interfaces\CommentInterface;

class CommentRepo  extends BaseRepo implements CommentInterface
{

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    public function getById($comment_id)
    {
        $data = Comment::findorfail($comment_id);
        return $data;
    }
}