<?php

namespace Facade\Ignition\Tests\TestClasses;

use DateTimeImmutable;
use Facade\FlareClient\Time\Time;

class FakeTime implements Time
{
    /** @var \DateTimeImmutable */
    protected $dateTime;

    public function __construct(string $dateTime = null, $format = 'Y-m-d H:i:s')
    {
        if (! is_null($dateTime)) {
            $this->setCurrentTime($dateTime, $format);

            return;
        }

        $this->dateTime = new DateTimeImmutable();
    }

    public function getCurrentTime(): int
    {
        return $this->dateTime->getTimestamp();
    }

    public function setCurrentTime(string $dateTime, $format = 'Y-m-d H:i:s')
    {
        $this->dateTime = DateTimeImmutable::createFromFormat($format, $dateTime);
    }
}
