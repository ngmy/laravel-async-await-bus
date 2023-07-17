<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

class TestCommandHandler
{
    public function handle(TestCommand $command): string
    {
        $command->respond('res');

        return 'retval';
    }
}
