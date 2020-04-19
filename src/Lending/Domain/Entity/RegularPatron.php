<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Chabior\Library\Common\Result;
use Chabior\Library\Lending\Domain\Policy\BookIsAvailablePolicy;
use Chabior\Library\Lending\Domain\Policy\BookIsRestrictedPolicy;
use Chabior\Library\Lending\Domain\Policy\CompositeHoldPolicy;
use Chabior\Library\Lending\Domain\Policy\MaximumNumberOfHoldsPolicy;
use Chabior\Library\Lending\Domain\Policy\MaximumNumberOfOverdueCheckoutPolicy;
use Chabior\Library\Lending\Domain\Reason\CanNotHoldForLessDayOneDayReason;

class RegularPatron extends Patron
{
    public function hold(Book $book, int $numberOfDays): Result
    {
        if ($numberOfDays < 1) {
            return Result::failure(new CanNotHoldForLessDayOneDayReason());
        }

        $policy = new CompositeHoldPolicy(
            new BookIsAvailablePolicy(),
            new BookIsRestrictedPolicy(),
            new MaximumNumberOfHoldsPolicy(),
            new MaximumNumberOfOverdueCheckoutPolicy(),
        );

        return $this->performHold($policy, $book, $numberOfDays);
    }
}
