<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Chabior\Library\Common\Result;
use Chabior\Library\Lending\Domain\Policy\BookIsAvailablePolicy;
use Chabior\Library\Lending\Domain\Policy\CompositeHoldPolicy;
use Chabior\Library\Lending\Domain\Policy\MaximumNumberOfOverdueCheckoutPolicy;

class Researcher extends Patron
{
    public function hold(Book $book): Result
    {
        $policy = new CompositeHoldPolicy(
            new BookIsAvailablePolicy(),
            new MaximumNumberOfOverdueCheckoutPolicy()
        );

        return $this->performHold($policy, $book, null);
    }


}
