<?php declare(strict_types=1);

namespace Chabior\Library\Common\Result;

use Chabior\Library\Common\Result;

class Failure extends Result
{
    private Reason $reason;

    public function __construct(Reason $reason)
    {
        $this->reason = $reason;
    }

    public function isSuccess(): bool
    {
        return false;
    }

    public function isFailure(): bool
    {
        return true;
    }

    public function events(): array
    {
        return [];
    }

    public function reason(): Reason
    {
        return $this->reason;
    }
}
