<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;

class TestAsyncAwaitWithoutRespondableTraitAndResponseIdentPropertyCommandHandler
{
    public function __construct(private readonly TransportContract $transport)
    {
    }

    public function handle(TestAsyncAwaitWithoutRespondableTraitAndResponseIdentPropertyCommand $command): string
    {
        \assert(property_exists($command, 'responseIdent'));
        $this->transport->respond($command->responseIdent, 'res');

        return 'retval';
    }
}
