<?php declare(strict_types=1);

namespace Chabior\Library\Common\Result;

use Chabior\Library\Common\Result;

class Success extends Result
{
    private array $events;

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function events(): array
    {
        return $this->events;
    }

    public function isSuccess(): bool
    {
        return true;
    }

    public function isFailure(): bool
    {
        return false;
    }

    public function reason(): Reason
    {
        return new OKReason();
    }
}
