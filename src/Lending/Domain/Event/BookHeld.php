<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Event;

use Chabior\Library\Common\DomainEvent;
use Ramsey\Uuid\UuidInterface;

class BookHeld implements DomainEvent
{
    private UuidInterface $bookId;

    private ?int $numberOfDays;

    public function __construct(UuidInterface $bookId, ?int $numberOfDays)
    {
        $this->bookId = $bookId;
        $this->numberOfDays = $numberOfDays;
    }

    public function getBookId(): UuidInterface
    {
        return $this->bookId;
    }

    public function getNumberOfDays(): ?int
    {
        return $this->numberOfDays;
    }
}
