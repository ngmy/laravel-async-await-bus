<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus;

use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Ngmy\LaravelAsyncAwaitBus\Dispatcher as DispatcherDecorator;
use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->extend(Dispatcher::class, fn (Dispatcher $dispatcher, Application $app): DispatcherDecorator => new DispatcherDecorator($dispatcher, $app->make(TransportContract::class)));
    }
}
