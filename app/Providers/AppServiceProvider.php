<?php

namespace App\Providers;

use App\Models\Institution;
use App\Observers\InstitutionObserver;
use App\Repositories\Contracts\InstitutionRepositoryInterface;
use App\Repositories\InstitutionRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository interface to implementation
        $this->app->bind(
            InstitutionRepositoryInterface::class,
            InstitutionRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        Institution::observe(InstitutionObserver::class);
    }
}
