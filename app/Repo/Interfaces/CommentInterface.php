<?php

namespace App\Repo\Interfaces;


interface CommentInterface extends BaseInterface
{
    public function getById($comment_id);

}