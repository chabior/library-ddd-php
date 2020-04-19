<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Policy;

use Chabior\Library\Common\Result\Reason;
use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\Patron;
use Chabior\Library\Lending\Domain\Reason\MaximumNumberOfOverdueCheckoutsExceededReason;

class MaximumNumberOfOverdueCheckoutPolicy implements HoldPolicy
{
    private const MAX_OVERDUE_CHECKOUTS = 2;

    public function isFulfilled(Patron $patron, Book $book): bool
    {
        return !$patron->hasMoreOrEqualsOverdueCheckouts(self::MAX_OVERDUE_CHECKOUTS);
    }

    public function reason(): Reason
    {
        return new MaximumNumberOfOverdueCheckoutsExceededReason();
    }
}
