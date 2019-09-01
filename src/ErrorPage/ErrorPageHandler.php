<?php

namespace Facade\Ignition\ErrorPage;

use Throwable;
use Facade\Ignition\IgnitionConfig;
use Illuminate\Foundation\Application;
use Facade\IgnitionContracts\SolutionProviderRepository;

class ErrorPageHandler
{
    /** @var \Facade\Ignition\IgnitionConfig */
    protected $ignitionConfig;

    /** @var \Facade\Ignition\Facades\Flare */
    protected $flareClient;

    /** @var \Facade\Ignition\ErrorPage\Renderer */
    protected $renderer;

    /** @var \Facade\IgnitionContracts\SolutionProviderRepository */
    protected $solutionProviderRepository;

    public function __construct(
        Application $app,
        IgnitionConfig $ignitionConfig,
        Renderer $renderer,
        SolutionProviderRepository $solutionProviderRepository
    ) {
        $this->flareClient = $app->make('flare.client');
        $this->ignitionConfig = $ignitionConfig;
        $this->renderer = $renderer;
        $this->solutionProviderRepository = $solutionProviderRepository;
    }

    public function handle(Throwable $throwable)
    {
        $report = $this->flareClient->createReport($throwable);

        $solutions = $this->solutionProviderRepository->getSolutionsForThrowable($throwable);

        $viewModel = new ErrorPageViewModel(
            $throwable,
            $this->ignitionConfig,
            $report,
            $solutions
        );

        $this->renderException($viewModel);
    }

    protected function renderException(ErrorPageViewModel $exceptionViewModel)
    {
        echo $this->renderer->render(
            'errorPage',
            $exceptionViewModel->toArray()
        );
    }
}
