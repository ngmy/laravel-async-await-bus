<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

class TestAsyncAwaitCommandHandler
{
    public function handle(TestAsyncAwaitCommand $command): string
    {
        $command->respond('res');

        return 'retval';
    }
}
