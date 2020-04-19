<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Policy;

use Chabior\Library\Common\Result\Reason;
use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\Patron;
use Chabior\Library\Lending\Domain\Reason\CanNotHoldForLessDayOneDayReason;

class NumberOfDaysLowerThanOnePolicy implements HoldPolicy
{
    public function isFulfilled(Patron $patron, Book $book, ?int $numberOfDays): bool
    {
        return ((int) $numberOfDays) > 0;
    }

    public function reason(): Reason
    {
        return new CanNotHoldForLessDayOneDayReason();
    }
}
