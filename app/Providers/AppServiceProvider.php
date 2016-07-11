<?php

namespace App\Providers;

use App\Games\Factorio\FactorioServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // send logs to stdout
        $logger = $this->app->make(\Psr\Log\LoggerInterface::class);
        $logger->popHandler();
        $logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());
    }
}
