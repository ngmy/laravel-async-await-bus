<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

use Illuminate\Contracts\Queue\ShouldQueue;

class TestAsyncCommandHandler implements ShouldQueue
{
    public function handle(TestAsyncCommand $command): string
    {
        $command->respond('res');

        return 'retval';
    }
}
