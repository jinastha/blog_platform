<?php

namespace App\Repo\Eloquent;

use App\Models\Category;
use App\Repo\Interfaces\CategoryInterface;

class CategoryRepo  extends BaseRepo implements CategoryInterface
{

    public function __construct(Category $model)
    {
        $this->model = $model;
    }
}