<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;

class TestAsyncAwaitWithoutRespondableTraitCommandHandler
{
    public function __construct(private readonly TransportContract $transport)
    {
    }

    public function handle(TestAsyncAwaitWithoutRespondableTraitCommand $command): string
    {
        $this->transport->respond($command->responseIdent, 'res');

        return 'retval';
    }
}
