<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Policy\BookIsAvailablePolicy;
use Chabior\Library\Lending\Domain\Policy\CompositeHoldPolicy;
use Chabior\Library\Lending\Domain\Policy\HoldPolicy;
use Chabior\Library\Lending\Domain\Policy\MaximumNumberOfOverdueCheckoutPolicy;

class Researcher extends Patron
{
    protected function createHoldPolicy(): HoldPolicy
    {
        return new CompositeHoldPolicy(
            new BookIsAvailablePolicy(),
            new MaximumNumberOfOverdueCheckoutPolicy()
        );
    }

    protected function createHold(Book $book, ?int $numberOfDays): Hold
    {
        return new OpenEndedHold($book, $this);
    }
}
