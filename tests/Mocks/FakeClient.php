<?php

namespace Facade\Ignition\Tests\Mocks;

use Facade\FlareClient\Http\Client;
use Facade\FlareClient\Http\Response;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert;

class FakeClient extends Client
{
    public $requests = [];

    public function __construct()
    {
        parent::__construct(uniqid(), null);
    }

    public function makeCurlRequest(string $verb, string $fullUrl, array $headers = [], array $arguments = []): Response
    {
        $this->requests[] = compact('verb', 'fullUrl', 'headers', 'arguments');

        return new Response(['http_code' => 200], 'my response', '');
    }

    public function assertRequestsSent(int $expectedCount)
    {
        Assert::assertCount($expectedCount, $this->requests);
    }

    public function getLastRequest(): array
    {
        return Arr::last($this->requests);
    }
}
