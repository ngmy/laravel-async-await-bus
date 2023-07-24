<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

class TestNonRespondableAsyncAwaitCommandHandler
{
    public function handle(TestNonRespondableAsyncAwaitCommand $command): string
    {
        return 'retval';
    }
}
