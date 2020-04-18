<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Chabior\Library\Common\Result;
use Chabior\Library\Lending\Domain\Event\BookHoled;
use Chabior\Library\Lending\Domain\Reason\BookIsNotAvailableReason;
use Chabior\Library\Lending\Domain\Reason\CanNotHoldRestrictedBookReason;
use Chabior\Library\Lending\Domain\Reason\MaximumNumberOfHoldsExceededReason;
use Chabior\Library\Lending\Domain\Reason\MaximumNumberOfOverdueCheckoutsExceededReason;
use Doctrine\Common\Collections\Collection;

class RegularPatron
{
    private int $id;

    private int $overdueCheckouts;

    private Collection $holds;

    public function __construct()
    {
        $this->overdueCheckouts = 0;
        $this->holds = 0;
    }

    public function hold(Book $book, int $numberOfDays): Result
    {
        if (!$book->isAvailable()) {
            return Result::failure(new BookIsNotAvailableReason());
        }

        if ($book->isRestricted()) {
            return Result::failure(new CanNotHoldRestrictedBookReason());
        }

        if ($this->holds > 5) {
            return Result::failure(new MaximumNumberOfHoldsExceededReason());
        }

        if ($this->overdueCheckouts > 2) {
            return Result::failure(new MaximumNumberOfOverdueCheckoutsExceededReason());
        }

        ++$this->holds;

        return Result::success(new BookHoled($book->getId(), $numberOfDays));
    }
}
