# Laravel Async Await Bus

[![Latest Stable Version](https://img.shields.io/packagist/v/ngmy/laravel-async-await-bus.svg?style=flat-square&label=stable)](https://packagist.org/packages/ngmy/laravel-async-await-bus)
[![Test Status](https://img.shields.io/github/actions/workflow/status/ngmy/laravel-async-await-bus/test.yml?style=flat-square&label=test)](https://github.com/ngmy/laravel-async-await-bus/actions/workflows/test.yml)
[![Lint Status](https://img.shields.io/github/actions/workflow/status/ngmy/laravel-async-await-bus/lint.yml?style=flat-square&label=lint)](https://github.com/ngmy/laravel-async-await-bus/actions/workflows/lint.yml)
[![Code Coverage](https://img.shields.io/coverallsCoverage/github/ngmy/laravel-async-await-bus?style=flat-square)](https://coveralls.io/github/ngmy/laravel-async-await-bus)
[![Total Downloads](https://img.shields.io/packagist/dt/ngmy/laravel-async-await-bus.svg?style=flat-square)](https://packagist.org/packages/ngmy/laravel-async-await-bus)

A Laravel bus decorator that allows to await asynchronous command responses.

## Installation

```bash
composer require ngmy/laravel-async-await-bus
```

## Usage

Command classes must implement the `ShouldAwaitResponse` interface and use the `Respondable` trait:

```php
<?php

namespace App\Commands;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ngmy\LaravelAsyncAwaitBus\Concerns\Respondable;
use Ngmy\LaravelAsyncAwaitBus\Contracts\ShouldAwaitResponse;

class CreateNewArticleCommand implements ShouldQueue, ShouldAwaitResponse
{
    use InteractsWithQueue, Queueable, SerializesModels, Respondable;

    public function __construct(
        public readonly User $user,
        public readonly string $title,
        public readonly string $body,
        public readonly bool $published,
    ) {
    }
}
```

Handler classes must contain a `handle` method or an `__invoke` method, and must respond using the `respond` method of
command instances:

```php
<?php

namespace App\Handlers\Commands;

use App\Commands\CreateNewArticleCommand;

class CreateNewArticleCommandHandler
{
    public function handle(CreateNewArticleCommand $command): void
    {
        $article = $command->user->articles()->create([
            'title' => $command->title,
            'body' => $command->body,
            'published' => $command->published,
            'published_at' => $command->published ? now() : null,
        ]);

        $command->respond($article->id);
    }
}
```

You need to register command and handler mappings. For example, you can register in the `boot` method of the
`AppServiceProvider` class:

```php
use App\Commands\CreateNewArticleCommand;
use App\Handlers\Commands\CreateNewArticleCommandHandler;
use Illuminate\Contracts\Bus\Dispatcher as Bus;

$bus = $this->app->make(Bus::class);
$bus->map([
    CreateNewArticleCommand::class => CreateNewArticleCommandHandler::class,
]);
```

Now, you can await asynchronous command responses:

```php
<?php

namespace App\Http\Controllers;

use App\Commands\CreateNewArticleCommand;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewArticleRequest;
use Illuminate\Contracts\Bus\Dispatcher as Bus;
use Illuminate\Http\RedirectResponse;

class CreateNewArticle extends Controller
{
    public function __invoke(CreateNewArticleRequest $request, Bus $bus): RedirectResponse
    {
        $command = new CreateNewArticleCommand(
            $request->user(),
            $request->string('title'),
            $request->string('body'),
            $request->boolean('published'),
        );
        $id = $bus->dispatch($command);

        return redirect("articles/{$id}");
    }
}
```

Of course, you can also use self-handling commands:

```php
<?php

namespace App\Commands;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ngmy\LaravelAsyncAwaitBus\Concerns\Respondable;
use Ngmy\LaravelAsyncAwaitBus\Contracts\ShouldAwaitResponse;

class CreateNewArticleCommand implements ShouldQueue, ShouldAwaitResponse
{
    use InteractsWithQueue, Queueable, SerializesModels, Respondable;

    public function __construct(
        public readonly User $user,
        public readonly string $title,
        public readonly string $body,
        public readonly bool $published,
    ) {
    }

    public function handle(): void
    {
        $article = $this->user->articles()->create([
            'title' => $this->title,
            'body' => $this->body,
            'published' => $this->published,
            'published_at' => $this->published ? now() : null,
        ]);

        $this->respond($article->id);
    }
}
```
