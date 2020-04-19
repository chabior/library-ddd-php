<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Policy;

use Chabior\Library\Common\Result\Reason;
use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\Patron;
use Chabior\Library\Lending\Domain\Reason\MaximumNumberOfHoldsExceededReason;

class MaximumNumberOfHoldsPolicy implements HoldPolicy
{
    private const MAXIMUM_NUMBER_OF_HOLD = 5;

    public function isFulfilled(Patron $patron, Book $book): bool
    {
        return !$patron->hasMoreOrEqualsHolds(self::MAXIMUM_NUMBER_OF_HOLD);
    }

    public function reason(): Reason
    {
        return new MaximumNumberOfHoldsExceededReason();
    }
}
