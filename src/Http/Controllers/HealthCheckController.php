<?php

namespace Facade\Ignition\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class HealthCheckController
{
    public function __invoke()
    {
        return [
            'can_execute_commands' => $this->canExecuteCommands(),
        ];
    }

    protected function canExecuteCommands(): bool
    {
        Artisan::call('help', ['--version']);

        $output = Artisan::output();

        return Str::contains($output, app()->version());
    }
}
