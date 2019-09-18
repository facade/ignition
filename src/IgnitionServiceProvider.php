<?php

namespace Facade\Ignition;

use Throwable;
use Monolog\Logger;
use Illuminate\Support\Arr;
use Facade\FlareClient\Flare;
use Illuminate\Log\LogManager;
use Illuminate\Queue\QueueManager;
use Facade\FlareClient\Http\Client;
use Illuminate\Support\Facades\Log;
use Whoops\Handler\HandlerInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Facade\Ignition\ErrorPage\Renderer;
use Facade\Ignition\Middleware\AddLogs;
use Illuminate\Support\ServiceProvider;
use Facade\Ignition\Logger\FlareHandler;
use Facade\Ignition\Middleware\AddDumps;
use Illuminate\Log\Events\MessageLogged;
use Facade\Ignition\Commands\TestCommand;
use Facade\Ignition\Middleware\AddQueries;
use Facade\Ignition\LogRecorder\LogRecorder;
use Facade\Ignition\Middleware\AddSolutions;
use Facade\Ignition\Views\Engines\PhpEngine;
use Facade\Ignition\DumpRecorder\DumpRecorder;
use Facade\Ignition\Middleware\SetNotifierName;
use Facade\Ignition\QueryRecorder\QueryRecorder;
use Facade\Ignition\Middleware\AddGitInformation;
use Facade\Ignition\Views\Engines\CompilerEngine;
use Facade\Ignition\Context\LaravelContextDetector;
use Facade\Ignition\ErrorPage\IgnitionWhoopsHandler;
use Facade\Ignition\Http\Middleware\IgnitionEnabled;
use Facade\Ignition\Http\Controllers\StyleController;
use Facade\Ignition\Http\Controllers\ScriptController;
use Facade\Ignition\Middleware\AddEnvironmentInformation;
use Illuminate\View\Engines\PhpEngine as LaravelPhpEngine;
use Facade\Ignition\Http\Controllers\HealthCheckController;
use Facade\Ignition\Http\Controllers\ShareReportController;
use Facade\Ignition\Http\Controllers\ExecuteSolutionController;
use Facade\Ignition\SolutionProviders\SolutionProviderRepository;
use Facade\Ignition\SolutionProviders\ViewNotFoundSolutionProvider;
use Facade\Ignition\SolutionProviders\BadMethodCallSolutionProvider;
use Facade\Ignition\SolutionProviders\DefaultDbNameSolutionProvider;
use Facade\Ignition\SolutionProviders\MergeConflictSolutionProvider;
use Facade\Ignition\SolutionProviders\MissingAppKeySolutionProvider;
use Facade\Ignition\SolutionProviders\MissingColumnSolutionProvider;
use Facade\Ignition\SolutionProviders\MissingImportSolutionProvider;
use Facade\Ignition\SolutionProviders\TableNotFoundSolutionProvider;
use Illuminate\View\Engines\CompilerEngine as LaravelCompilerEngine;
use Facade\Ignition\SolutionProviders\MissingPackageSolutionProvider;
use Facade\Ignition\SolutionProviders\InvalidRouteActionSolutionProvider;
use Facade\Ignition\SolutionProviders\IncorrectValetDbCredentialsSolutionProvider;
use Facade\IgnitionContracts\SolutionProviderRepository as SolutionProviderRepositoryContract;

class IgnitionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/flare.php' => config_path('flare.php'),
            ], 'flare-config');

            $this->publishes([
                __DIR__.'/../config/ignition.php' => config_path('ignition.php'),
            ], 'ignition-config');
        }

        $this
            ->registerViewEngines()
            ->setupQueue($this->app->queue);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/flare.php', 'flare');
        $this->mergeConfigFrom(__DIR__.'/../config/ignition.php', 'ignition');

        $this
            ->registerHousekeepingRoutes()
            ->registerSolutionProviderRepository()
            ->registerExceptionRenderer()
            ->registerWhoopsHandler()
            ->registerIgnitionConfig()
            ->registerFlare()
            ->registerLogHandler()
            ->registerLogRecorder()
            ->registerDumpCollector()
            ->registerCommands();

        if (config('flare.reporting.report_queries')) {
            $this->registerQueryRecorder();
        }

        if (config('flare.reporting.anonymize_ips')) {
            $this->app->get('flare.client')->anonymizeIp();
        }

        $this->registerBuiltInMiddleware();
    }

    protected function registerViewEngines()
    {
        if (! $this->hasCustomViewEnginesRegistered()) {
            return $this;
        }

        $this->app->make('view.engine.resolver')->register('php', function () {
            return new PhpEngine();
        });

        $this->app->make('view.engine.resolver')->register('blade', function () {
            return new CompilerEngine($this->app['blade.compiler']);
        });

        return $this;
    }

    protected function registerHousekeepingRoutes()
    {
        Route::group([
            'prefix' => config('ignition.housekeeping_endpoint_prefix', '_ignition'),
            'middleware' => [IgnitionEnabled::class],
        ], function () {
            Route::get('health-check', HealthCheckController::class);
            Route::post('execute-solution', ExecuteSolutionController::class);
            Route::post('share-report', ShareReportController::class);

            Route::get('scripts/{script}', ScriptController::class);
            Route::get('styles/{style}', StyleController::class);
        });

        return $this;
    }

    protected function registerSolutionProviderRepository()
    {
        $this->app->singleton(SolutionProviderRepositoryContract::class, function () {
            $defaultSolutions = $this->getDefaultSolutions();

            return new SolutionProviderRepository($defaultSolutions);
        });

        return $this;
    }

    protected function registerExceptionRenderer()
    {
        $this->app->bind(Renderer::class, function () {
            return new Renderer(__DIR__.'/../resources/views/');
        });

        return $this;
    }

    protected function registerWhoopsHandler()
    {
        $this->app->bind(HandlerInterface::class, function (Application $app) {
            return $app->make(IgnitionWhoopsHandler::class);
        });

        return $this;
    }

    protected function registerIgnitionConfig()
    {
        $this->app->singleton(IgnitionConfig::class, function () {
            $options = [];

            try {
                if ($configPath = $this->getConfigFileLocation()) {
                    $options = require $configPath;
                }
            } catch (Throwable $e) {
                // possible open_basedir restriction
            }

            return new IgnitionConfig($options);
        });

        return $this;
    }

    protected function registerFlare()
    {
        $this->app->singleton('flare.http', function () {
            return new Client(
                config('flare.key'),
                config('flare.secret'),
                config('flare.base_url', 'https://flareapp.io/api')
            );
        });

        $this->app->alias('flare.http', Client::class);

        $this->app->singleton('flare.client', function () {
            $client = new Flare($this->app->get('flare.http'), new LaravelContextDetector, $this->app);
            $client->applicationPath(base_path());
            $client->stage(config('app.env'));

            return $client;
        });

        $this->app->alias('flare.client', Flare::class);

        return $this;
    }

    protected function registerLogHandler()
    {
        $this->app->singleton('flare.logger', function ($app) {
            $logger = new Logger('Flare');
            $logger->pushHandler(new FlareHandler($app->make('flare.client')));

            return $logger;
        });

        if ($this->app['log'] instanceof LogManager) {
            Log::extend('flare', function ($app) {
                return $app['flare.logger'];
            });
        } else {
            $this->bindLogListener();
        }

        return $this;
    }

    protected function registerLogRecorder()
    {
        $logCollector = $this->app->make(LogRecorder::class)->register();

        $this->app->singleton(LogRecorder::class);

        $this->app->instance(LogRecorder::class, $logCollector);

        return $this;
    }

    protected function registerDumpCollector()
    {
        $dumpCollector = $this->app->make(DumpRecorder::class)->register();

        $this->app->singleton(DumpRecorder::class);

        $this->app->instance(DumpRecorder::class, $dumpCollector);

        return $this;
    }

    protected function registerCommands()
    {
        $this->app->bind('command.flare:test', TestCommand::class);

        $this->commands([
            'command.flare:test',
        ]);
    }

    protected function registerQueryRecorder()
    {
        $queryCollector = $this->app->make(QueryRecorder::class)->register();

        $this->app->singleton(QueryRecorder::class);

        $this->app->instance(QueryRecorder::class, $queryCollector);

        return $this;
    }

    protected function registerBuiltInMiddleware()
    {
        $middleware = collect([
            SetNotifierName::class,
            AddEnvironmentInformation::class,
            AddLogs::class,
            AddDumps::class,
            AddQueries::class,
            AddSolutions::class,
        ])
        ->map(function (string $middlewareClass) {
            return $this->app->make($middlewareClass);
        });

        if (config('flare.reporting.collect_git_information')) {
            $middleware[] = (new AddGitInformation());
        }

        foreach ($middleware as $singleMiddleware) {
            $this->app->get('flare.client')->registerMiddleware($singleMiddleware);
        }

        return $this;
    }

    protected function getDefaultSolutions(): array
    {
        return [
            IncorrectValetDbCredentialsSolutionProvider::class,
            MissingAppKeySolutionProvider::class,
            DefaultDbNameSolutionProvider::class,
            BadMethodCallSolutionProvider::class,
            TableNotFoundSolutionProvider::class,
            MissingImportSolutionProvider::class,
            MissingPackageSolutionProvider::class,
            InvalidRouteActionSolutionProvider::class,
            ViewNotFoundSolutionProvider::class,
            MergeConflictSolutionProvider::class,
            MissingColumnSolutionProvider::class,
        ];
    }

    protected function hasCustomViewEnginesRegistered()
    {
        $resolver = $this->app->make('view.engine.resolver');

        if (! $resolver->resolve('php') instanceof LaravelPhpEngine) {
            return false;
        }

        if (! $resolver->resolve('blade') instanceof LaravelCompilerEngine) {
            return false;
        }

        return true;
    }

    protected function bindLogListener()
    {
        $this->app['log']->listen(function (MessageLogged $messageLogged) {
            if (config('flare.key')) {
                try {
                    $this->app['flare.logger']->log(
                        $messageLogged->level,
                        $messageLogged->message,
                        $messageLogged->context
                    );
                } catch (Exception $exception) {
                    return;
                }
            }
        });
    }

    protected function getConfigFileLocation(): ?string
    {
        $configFullPath = base_path().DIRECTORY_SEPARATOR.'.ignition';

        if (file_exists($configFullPath)) {
            return $configFullPath;
        }

        $configFullPath = Arr::get($_SERVER, 'HOME', '').DIRECTORY_SEPARATOR.'.ignition';

        if (file_exists($configFullPath)) {
            return $configFullPath;
        }

        return null;
    }

    protected function setupQueue(QueueManager $queue)
    {
        $queue->looping(function () {
            $this->app->get('flare.client')->reset();

            if (config('flare.reporting.report_queries')) {
                $this->app->make(QueryRecorder::class)->reset();
            }

            $this->app->make(LogRecorder::class)->reset();

            $this->app->make(DumpRecorder::class)->reset();
        });
    }
}
