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
        $this->app->bind(
            \App\Repositories\Contracts\TeacherRepositoryInterface::class,
            \App\Repositories\Eloquent\TeacherRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\LessonPreparationRepositoryInterface::class,
            \App\Repositories\LessonPreparationRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\LessonRepositoryInterface::class,
            \App\Repositories\LessonRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        Institution::observe(InstitutionObserver::class);

        // Production Safety: Disable destructive commands
        $this->disableDestructiveCommands();
    }

    /**
     * Disable destructive database commands in production
     */
    private function disableDestructiveCommands(): void
    {
        if ($this->app->environment('production', 'staging')) {
            // Disable destructive migration commands
            $destructiveCommands = [
                'migrate:fresh',
                'migrate:refresh',
                'migrate:reset',
                'db:wipe',
            ];

            foreach ($destructiveCommands as $command) {
                $this->app->extend("command.{$command}", function () {
                    return new class {
                        public function __invoke()
                        {
                            throw new \RuntimeException(
                                '🚨 DESTRUCTIVE COMMAND BLOCKED: This command is disabled in production/staging environments to prevent data loss. '.
                                'If you absolutely need to run this, temporarily set APP_ENV=local in your .env file.'
                            );
                        }
                    };
                });
            }
        }
    }
}
