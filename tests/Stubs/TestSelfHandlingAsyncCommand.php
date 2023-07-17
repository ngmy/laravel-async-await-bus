<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Ngmy\LaravelAsyncAwaitBus\Concerns\Respondable;

class TestSelfHandlingAsyncCommand implements ShouldQueue
{
    use Queueable;
    use Respondable;

    public function handle(): string
    {
        $this->respond('res');

        return 'retval';
    }
}
