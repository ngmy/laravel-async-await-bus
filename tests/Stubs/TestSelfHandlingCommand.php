<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests\Stubs;

use Ngmy\LaravelAsyncAwaitBus\Concerns\Respondable;

class TestSelfHandlingCommand
{
    use Respondable;

    public function handle(): string
    {
        $this->respond('res');

        return 'retval';
    }
}
