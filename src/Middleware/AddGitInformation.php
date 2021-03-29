<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;
use ReflectionClass;
use Symfony\Component\Process\Process;

class AddGitInformation
{
    public function handle(Report $report, $next)
    {
        $report->group('git', [
            'hash' => $this->hash(),
            'message' => $this->message(),
            'tag' => $this->tag(),
            'remote' => $this->remote(),
            'isDirty' => ! $this->isClean(),
        ]);

        return $next($report);
    }

    public function hash(): ?string
    {
        return $this->command("git log --pretty=format:'%H' -n 1");
    }

    public function message(): ?string
    {
        return $this->command("git log --pretty=format:'%s' -n 1");
    }

    public function tag(): ?string
    {
        return $this->command('git describe --tags --abbrev=0');
    }

    public function remote(): ?string
    {
        return $this->command('git config --get remote.origin.url');
    }

    public function isClean(): bool
    {
        return empty($this->command('git status -s'));
    }

    protected function command($command)
    {
        $process = (new ReflectionClass(Process::class))->hasMethod('fromShellCommandline')
            ? Process::fromShellCommandline($command, base_path())
            : new Process($command, base_path());

        $process->run();

        return trim($process->getOutput());
    }
}
