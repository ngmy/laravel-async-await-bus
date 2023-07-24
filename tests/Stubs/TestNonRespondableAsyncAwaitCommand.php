<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Ngmy\LaravelAsyncAwaitBus\Contracts\ShouldAwaitResponse;

class TestNonRespondableAsyncAwaitCommand implements ShouldQueue, ShouldAwaitResponse
{
    use Queueable;
}
