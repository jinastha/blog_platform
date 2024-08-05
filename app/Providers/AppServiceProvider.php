<?php

namespace App\Providers;

use App\Repo\Eloquent\AuthRepo;
use Illuminate\Support\ServiceProvider;


use App\Repo\Eloquent\BaseRepo;
use App\Repo\Eloquent\CategoryRepo;
use App\Repo\Eloquent\CommentRepo;
use App\Repo\Eloquent\PostRepo;
use App\Repo\Eloquent\TagRepo;
use App\Repo\Eloquent\UserRepo;
use App\Repo\Interfaces\BaseInterface;
use App\Repo\Interfaces\CategoryInterface;
use App\Repo\Interfaces\CommentInterface;
use App\Repo\Interfaces\PostInterface;
use App\Repo\Interfaces\TagInterface;
use App\Repo\Interfaces\UserInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BaseInterface::class, BaseRepo::class);
        $this->app->bind(CategoryInterface::class, CategoryRepo::class);
        $this->app->bind(CommentInterface::class, CommentRepo::class);
        $this->app->bind(PostInterface::class, PostRepo::class);
        $this->app->bind(TagInterface::class, TagRepo::class);
        $this->app->bind(UserInterface::class, UserRepo::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
