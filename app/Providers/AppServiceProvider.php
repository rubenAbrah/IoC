<?php

namespace App\Providers;

use Exception;
use App\Handlers\CommandQueueHandler;
use Illuminate\Support\ServiceProvider;
use App\Handlers\RetryTwiceThenLogHandler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register()
    {
        $this->app->singleton(CommandQueueHandler::class, function ($app) {
            $queueHandler = new CommandQueueHandler();

            // Регистрируем стратегию "Повторить два раза, затем записать в лог"
            $queueHandler->addExceptionHandler(
                Exception::class,
                new RetryTwiceThenLogHandler($queueHandler)
            );

            return $queueHandler;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}