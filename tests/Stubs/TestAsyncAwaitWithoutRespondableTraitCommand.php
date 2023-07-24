<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Ngmy\LaravelAsyncAwaitBus\Contracts\ShouldAwaitResponse;
use Williamjulianvicary\LaravelJobResponse\Facades\LaravelJobResponse;

class TestAsyncAwaitWithoutRespondableTraitCommand implements ShouldQueue, ShouldAwaitResponse
{
    use Queueable;

    public string $responseIdent;

    public function __construct()
    {
        $this->responseIdent = LaravelJobResponse::generateIdent(static::class);
    }
}
