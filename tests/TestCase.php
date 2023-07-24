<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PHPUnit\Runner\Version;

abstract class TestCase extends OrchestraTestCase
{
    protected $enablesPackageDiscoveries = true;

    /**
     * @param mixed[]    $data
     * @param int|string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        if ((int) Version::id() >= 10) {
            \assert(\is_string($name));
            parent::__construct($name);
        } else {
            // @phpstan-ignore-next-line
            parent::__construct($name, $data, $dataName);
        }

        // Add to use facades in data providers.
        $this->createApplication();
    }
}
