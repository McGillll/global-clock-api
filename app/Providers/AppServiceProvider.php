<?php

namespace App\Providers;

use App\Repositories\Contracts\CountdownRepositoryInterface;
use App\Repositories\Contracts\CountdownSequenceRepositoryInterface;
use App\Repositories\Eloquent\EloquentCountdownRepository;
use App\Repositories\Eloquent\EloquentCountdownSequenceRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CountdownRepositoryInterface::class, EloquentCountdownRepository::class);
        $this->app->bind(CountdownSequenceRepositoryInterface::class, EloquentCountdownSequenceRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
