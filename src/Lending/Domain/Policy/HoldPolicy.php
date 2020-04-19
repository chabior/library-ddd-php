<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Policy;

use Chabior\Library\Common\Result\Reason;
use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\Patron;

interface HoldPolicy
{
    public function isFulfilled(Patron $patron, Book $book, ?int $numberOfDays): bool;

    public function reason(): Reason;
}
