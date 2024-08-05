<?php

namespace App\Repo\Eloquent;

use App\Models\Post;
use App\Repo\Interfaces\PostInterface;

class PostRepo  extends BaseRepo implements PostInterface
{

    public function __construct(Post $model)
    {
        $this->model = $model;
    }
}