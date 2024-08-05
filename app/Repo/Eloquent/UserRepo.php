<?php

namespace App\Repo\Eloquent;

use App\Models\User;
use App\Repo\Interfaces\UserInterface;

class UserRepo  extends BaseRepo implements UserInterface
{

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}