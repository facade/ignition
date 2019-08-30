<?php

namespace Facade\Ignition\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Facade\IgnitionContracts\SolutionProviderRepository;
use Facade\Ignition\Http\Requests\ExecuteSolutionRequest;

class ExecuteSolutionController
{
    use ValidatesRequests;

    public function __invoke(
        ExecuteSolutionRequest $request,
        SolutionProviderRepository $solutionProviderRepository
    ) {
        $solution = $request->getRunnableSolution();

        $solution->run($request->get('parameters', []));

        return response('');
    }
}
