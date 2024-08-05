<?php

namespace App\Repo\Eloquent;

use App\Models\Tag;
use App\Repo\Interfaces\TagInterface;

class TagRepo  extends BaseRepo implements TagInterface
{

    public function __construct(Tag $model)
    {
        $this->model = $model;
    }
}