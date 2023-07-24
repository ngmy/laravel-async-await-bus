<?php

declare(strict_types=1);

namespace Ngmy\LaravelAsyncAwaitBus;

use Illuminate\Bus\Dispatcher as DispatcherDecoratee;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Ngmy\LaravelAsyncAwaitBus\Contracts\ShouldAwaitResponse;
use Williamjulianvicary\LaravelJobResponse\Response;
use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;

/**
 * @method null|\Illuminate\Bus\Batch              findBatch(string $batchId)
 * @method \Illuminate\Bus\PendingBatch            batch(array|\Illuminate\Support\Collection|mixed $jobs)
 * @method \Illuminate\Foundation\Bus\PendingChain chain(array|\Illuminate\Support\Collection $jobs)
 * @method void                                    dispatchAfterResponse(mixed $command, mixed $handler = null)
 */
class Dispatcher implements DispatcherContract
{
    public function __construct(
        private readonly DispatcherDecoratee $dispatcher,
        private readonly TransportContract $transport,
    ) {
    }

    /**
     * @param mixed[] $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->dispatcher->{$method}(...$parameters);
    }

    public function dispatch(mixed $command): mixed
    {
        return $this->executeDispatcher($command, fn (): mixed => $this->dispatcher->dispatch($command));
    }

    public function dispatchSync(mixed $command, mixed $handler = null): mixed
    {
        return $this->executeDispatcher($command, fn (): mixed => $this->dispatcher->dispatchSync($command, $handler));
    }

    public function dispatchNow(mixed $command, mixed $handler = null): mixed
    {
        return $this->executeDispatcher($command, fn (): mixed => $this->dispatcher->dispatchNow($command, $handler), forceNotAwait: true);
    }

    public function hasCommandHandler(mixed $command): bool
    {
        return $this->dispatcher->hasCommandHandler($command);
    }

    public function getCommandHandler(mixed $command): mixed
    {
        return $this->dispatcher->getCommandHandler($command);
    }

    public function dispatchToQueue(mixed $command): mixed
    {
        return $this->executeDispatcher($command, fn (): mixed => $this->dispatcher->dispatchToQueue($command));
    }

    /**
     * @param mixed[] $pipes
     */
    public function pipeThrough(array $pipes): static
    {
        $this->dispatcher->pipeThrough($pipes);

        return $this;
    }

    /**
     * @param mixed[] $map
     */
    public function map(array $map): static
    {
        $this->dispatcher->map($map);

        return $this;
    }

    public function dispatchAsyncAwait(mixed $command): mixed
    {
        return $this->executeDispatcher($command, fn (): mixed => $this->dispatcher->dispatchToQueue($command), forceAwait: true);
    }

    private function executeDispatcher(mixed $command, \Closure $dispatcher, bool $forceAwait = false, bool $forceNotAwait = false): mixed
    {
        // The type declarations for the $command parameters in the Laravel bus are mixed types,
        // but it looks to me like they only work for objects.
        \assert(\is_object($command));

        if (method_exists($command, 'prepareResponse')) {
            $command->prepareResponse();
        }

        if ($forceNotAwait || (!$forceAwait && !$this->shouldAwaitResponse($command)) || !$this->canRespond($command)) {
            return $dispatcher();
        }

        $dispatcher();

        return $this->awaitResponse($command);
    }

    private function shouldAwaitResponse(object $command): bool
    {
        return $command instanceof ShouldQueue && $command instanceof ShouldAwaitResponse;
    }

    private function canRespond(object $command): bool
    {
        return method_exists($command, 'getResponseIdent') || isset($command->responseIdent);
    }

    private function awaitResponse(object $command): mixed
    {
        if (method_exists($command, 'getResponseIdent')) {
            $responseIdent = $command->getResponseIdent();
        } else {
            \assert(isset($command->responseIdent));
            $responseIdent = $command->responseIdent;
        }

        $response = $this->transport
            ->throwExceptionOnFailure(true)
            ->awaitResponse($responseIdent, $command->timeout ?? 60)
        ;
        \assert($response instanceof Response);

        return $response->getData();
    }
}
