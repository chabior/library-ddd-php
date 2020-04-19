<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Policy;

use Chabior\Library\Common\Result\Reason;
use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\Patron;

class CompositeHoldPolicy implements HoldPolicy
{
    /** @var HoldPolicy[] */
    private array $policies;

    private ?Reason $reason;

    public function __construct(HoldPolicy ...$policies)
    {
        $this->policies = $policies;
    }

    public function isFulfilled(Patron $patron, Book $book): bool
    {
        foreach ($this->policies as $policy) {
            if (!$policy->isFulfilled($patron, $book)) {
                $this->reason = $policy->reason();
                return false;
            }
        }

        return true;
    }

    public function reason(): Reason
    {
        return $this->reason;
    }
}
