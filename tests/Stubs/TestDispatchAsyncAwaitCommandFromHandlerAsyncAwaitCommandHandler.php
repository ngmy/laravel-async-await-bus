<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

use Illuminate\Contracts\Bus\Dispatcher;

class TestDispatchAsyncAwaitCommandFromHandlerAsyncAwaitCommandHandler
{
    public function __construct(private readonly Dispatcher $bus)
    {
    }

    public function handle(TestDispatchAsyncAwaitCommandFromHandlerAsyncAwaitCommand $command): void
    {
        $response = $this->bus->dispatch(new TestAsyncAwaitCommand());

        $command->respond($response);
    }
}
