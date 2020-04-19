<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Policy\BookIsAvailablePolicy;
use Chabior\Library\Lending\Domain\Policy\BookIsRestrictedPolicy;
use Chabior\Library\Lending\Domain\Policy\CompositeHoldPolicy;
use Chabior\Library\Lending\Domain\Policy\HoldPolicy;
use Chabior\Library\Lending\Domain\Policy\MaximumNumberOfHoldsPolicy;
use Chabior\Library\Lending\Domain\Policy\MaximumNumberOfOverdueCheckoutPolicy;
use Chabior\Library\Lending\Domain\Policy\NumberOfDaysLowerThanOnePolicy;

class RegularPatron extends Patron
{
    protected function createHoldPolicy(): HoldPolicy
    {
        return new CompositeHoldPolicy(
            new BookIsAvailablePolicy(),
            new BookIsRestrictedPolicy(),
            new MaximumNumberOfHoldsPolicy(),
            new MaximumNumberOfOverdueCheckoutPolicy(),
            new NumberOfDaysLowerThanOnePolicy(),
        );
    }

    protected function createHold(Book $book, ?int $numberOfDays): Hold
    {
        return new ClosedEndedHold($book, $this, $numberOfDays);
    }
}
