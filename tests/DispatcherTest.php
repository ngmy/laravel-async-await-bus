<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus\Tests;

use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Facades\Bus;
use Ngmy\LaravelAsyncAwaitBus\Dispatcher;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestAsyncAwaitCommand;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestAsyncAwaitCommandHandler;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestAsyncCommand;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestAsyncCommandHandler;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestCommand;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestCommandHandler;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestDispatchAsyncAwaitCommandFromHandlerAsyncAwaitCommand;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestDispatchAsyncAwaitCommandFromHandlerAsyncAwaitCommandHandler;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestSelfHandlingAsyncAwaitCommand;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestSelfHandlingAsyncCommand;
use Ngmy\LaravelAsyncAwaitBus\Tests\Stubs\TestSelfHandlingCommand;

/**
 * @internal
 *
 * @covers \Ngmy\LaravelAsyncAwaitBus\Concerns\Respondable
 * @covers \Ngmy\LaravelAsyncAwaitBus\Dispatcher
 * @covers \Ngmy\LaravelAsyncAwaitBus\ServiceProvider
 */
final class DispatcherTest extends TestCase
{
    /**
     * @return mixed[]
     */
    public static function provideDispatchCases(): iterable
    {
        return [
            'command' => [
                [
                    TestCommand::class => TestCommandHandler::class,
                ],
                new TestCommand(),
                'retval',
            ],
            'async command' => [
                [
                    TestAsyncCommand::class => TestAsyncCommandHandler::class,
                ],
                new TestAsyncCommand(),
                0,
            ],
            'async/await command' => [
                [
                    TestAsyncAwaitCommand::class => TestAsyncAwaitCommandHandler::class,
                ],
                new TestAsyncAwaitCommand(),
                'res',
            ],
            'self-handling command' => [
                [],
                new TestSelfHandlingCommand(),
                'retval',
            ],
            'self-handling async command' => [
                [],
                new TestSelfHandlingAsyncCommand(),
                0,
            ],
            'self-handling async/await command' => [
                [],
                new TestSelfHandlingAsyncAwaitCommand(),
                'res',
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function provideDispatchSyncCases(): iterable
    {
        return [
            'command' => [
                [
                    TestCommand::class => TestCommandHandler::class,
                ],
                new TestCommand(),
                'retval',
            ],
            'async command' => [
                [
                    TestAsyncCommand::class => TestAsyncCommandHandler::class,
                ],
                new TestAsyncCommand(),
                0,
            ],
            'async/await command' => [
                [
                    TestAsyncAwaitCommand::class => TestAsyncAwaitCommandHandler::class,
                ],
                new TestAsyncAwaitCommand(),
                'res',
            ],
            'self-handling command' => [
                [],
                new TestSelfHandlingCommand(),
                'retval',
            ],
            'self-handling async command' => [
                [],
                new TestSelfHandlingAsyncCommand(),
                0,
            ],
            'self-handling async/await command' => [
                [],
                new TestSelfHandlingAsyncAwaitCommand(),
                'res',
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function provideDispatchNowCases(): iterable
    {
        return [
            'command' => [
                [
                    TestCommand::class => TestCommandHandler::class,
                ],
                new TestCommand(),
                'retval',
            ],
            'async command' => [
                [
                    TestAsyncCommand::class => TestAsyncCommandHandler::class,
                ],
                new TestAsyncCommand(),
                'retval',
            ],
            'async/await command' => [
                [
                    TestAsyncAwaitCommand::class => TestAsyncAwaitCommandHandler::class,
                ],
                new TestAsyncAwaitCommand(),
                'retval',
            ],
            'self-handling command' => [
                [],
                new TestSelfHandlingCommand(),
                'retval',
            ],
            'self-handling async command' => [
                [],
                new TestSelfHandlingAsyncCommand(),
                'retval',
            ],
            'self-handling async/await command' => [
                [],
                new TestSelfHandlingAsyncAwaitCommand(),
                'retval',
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function provideDispatchToQueueCases(): iterable
    {
        return [
            'command' => [
                [
                    TestCommand::class => TestCommandHandler::class,
                ],
                new TestCommand(),
                0,
            ],
            'async command' => [
                [
                    TestAsyncCommand::class => TestAsyncCommandHandler::class,
                ],
                new TestAsyncCommand(),
                0,
            ],
            'async/await command' => [
                [
                    TestAsyncAwaitCommand::class => TestAsyncAwaitCommandHandler::class,
                ],
                new TestAsyncAwaitCommand(),
                'res',
            ],
            'self-handling command' => [
                [],
                new TestSelfHandlingCommand(),
                0,
            ],
            'self-handling async command' => [
                [],
                new TestSelfHandlingAsyncCommand(),
                0,
            ],
            'self-handling async/await command' => [
                [],
                new TestSelfHandlingAsyncAwaitCommand(),
                'res',
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function provideDispatchAsyncAwaitCases(): iterable
    {
        return [
            'command' => [
                [
                    TestCommand::class => TestCommandHandler::class,
                ],
                new TestCommand(),
                'res',
            ],
            'async command' => [
                [
                    TestAsyncCommand::class => TestAsyncCommandHandler::class,
                ],
                new TestAsyncCommand(),
                'res',
            ],
            'async/await command' => [
                [
                    TestAsyncAwaitCommand::class => TestAsyncAwaitCommandHandler::class,
                ],
                new TestAsyncAwaitCommand(),
                'res',
            ],
            'self-handling command' => [
                [],
                new TestSelfHandlingCommand(),
                'res',
            ],
            'self-handling async command' => [
                [],
                new TestSelfHandlingAsyncCommand(),
                'res',
            ],
            'self-handling async/await command' => [
                [],
                new TestSelfHandlingAsyncAwaitCommand(),
                'res',
            ],
        ];
    }

    public function testCall(): void
    {
        $bus = $this->createBus();

        $actual = $bus->chain([
            new TestCommand(),
            new TestAsyncCommand(),
            new TestAsyncAwaitCommand(),
        ]);

        self::assertInstanceOf(PendingChain::class, $actual);
    }

    /**
     * @dataProvider provideDispatchCases
     *
     * @param array<class-string, class-string> $map
     */
    public function testDispatch(array $map, mixed $command, mixed $expected): void
    {
        $bus = $this->createBus();

        $this->registerHandlers($bus, $map);

        $actual = $bus->dispatch($command);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideDispatchCases
     *
     * @param array<class-string, class-string> $map
     */
    public function testDispatchByFacade(array $map, mixed $command, mixed $expected): void
    {
        $bus = $this->createBus();

        $this->registerHandlers($bus, $map);

        $actual = Bus::dispatch($command);

        self::assertSame($expected, $actual);
    }

    public function testDispatchTestDispatchAsyncAwaitCommandFromHandlerAsyncAwaitCommand(): void
    {
        $bus = $this->createBus();

        $bus->map([
            TestAsyncAwaitCommand::class => TestAsyncAwaitCommandHandler::class,
            TestDispatchAsyncAwaitCommandFromHandlerAsyncAwaitCommand::class => TestDispatchAsyncAwaitCommandFromHandlerAsyncAwaitCommandHandler::class,
        ]);

        $actual = $bus->dispatch(new TestDispatchAsyncAwaitCommandFromHandlerAsyncAwaitCommand());

        self::assertSame('res', $actual);
    }

    /**
     * @dataProvider provideDispatchSyncCases
     *
     * @param array<class-string, class-string> $map
     */
    public function testDispatchSync(array $map, mixed $command, mixed $expected): void
    {
        $bus = $this->createBus();

        $this->registerHandlers($bus, $map);

        $actual = $bus->dispatchSync($command);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideDispatchNowCases
     *
     * @param array<class-string, class-string> $map
     */
    public function testDispatchNow(array $map, mixed $command, mixed $expected): void
    {
        $bus = $this->createBus();

        $this->registerHandlers($bus, $map);

        $actual = $bus->dispatchNow($command);

        self::assertSame($expected, $actual);
    }

    public function testHasCommandHandler(): void
    {
        $bus = $this->createBus();

        $this->registerHandlers($bus, [TestCommand::class => TestCommandHandler::class]);

        $actual = $bus->hasCommandHandler(new TestCommand());

        self::assertTrue($actual);
    }

    /**
     * @dataProvider provideDispatchToQueueCases
     *
     * @param array<class-string, class-string> $map
     */
    public function testDispatchToQueue(array $map, mixed $command, mixed $expected): void
    {
        $bus = $this->createBus();

        $this->registerHandlers($bus, $map);

        $actual = $bus->dispatchToQueue($command);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideDispatchCases
     *
     * @param array<class-string, class-string> $map
     */
    public function testPipeThrough(array $map, mixed $command, mixed $expected): void
    {
        $bus = $this->createBus();

        $this->registerHandlers($bus, $map);

        $bus->pipeThrough([fn (mixed $job, callable $next): mixed => $next($job)]);

        $actual = $bus->dispatch($command);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideDispatchAsyncAwaitCases
     *
     * @param array<class-string, class-string> $map
     */
    public function testDispatchAsyncAwait(array $map, mixed $command, mixed $expected): void
    {
        $bus = $this->createBus();

        $this->registerHandlers($bus, $map);

        $actual = $bus->dispatchAsyncAwait($command);

        self::assertSame($expected, $actual);
    }

    private function createBus(): Dispatcher
    {
        \assert($this->app instanceof Application);

        return $this->app->make(DispatcherContract::class);
    }

    /**
     * @param array<class-string, class-string> $map
     */
    private function registerHandlers(Dispatcher $bus, array $map): void
    {
        if (empty($map)) {
            return;
        }

        $bus->map($map);
    }
}
