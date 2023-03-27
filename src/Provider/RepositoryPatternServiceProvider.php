<?php

namespace Raahin\RepositoryPattern\Provider;


use Illuminate\Support\ServiceProvider;
use Raahin\RepositoryPattern\BaseRepository;
use Raahin\RepositoryPattern\Contracts\RepositoryInterface;

class RepositoryPatternServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, BaseRepository::class);
    }

    public function boot()
    {

    }
}