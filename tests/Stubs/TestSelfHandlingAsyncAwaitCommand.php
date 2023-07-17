<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Ngmy\LaravelAsyncAwaitBus\Concerns\Respondable;
use Ngmy\LaravelAsyncAwaitBus\Contracts\ShouldAwaitResponse;

class TestSelfHandlingAsyncAwaitCommand implements ShouldQueue, ShouldAwaitResponse
{
    use Queueable;
    use Respondable;

    public function handle(): string
    {
        $this->respond('res');

        return 'retval';
    }
}
