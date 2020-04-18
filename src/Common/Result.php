<?php declare(strict_types=1);

namespace Chabior\Library\Common;

use Chabior\Library\Common\Result\Failure;
use Chabior\Library\Common\Result\Reason;
use Chabior\Library\Common\Result\Success;

abstract class Result
{
    abstract public function isSuccess(): bool;
    abstract public function isFailure(): bool;
    abstract public function events(): array;
    abstract public function reason(): Reason;

    public static function success(DomainEvent ...$events): self
    {
        return new Success($events);
    }

    public static function failure(Reason $reason): self
    {
        return new Failure($reason);
    }
}
