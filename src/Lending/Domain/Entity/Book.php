<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Entity\BookType\CirculatingBookType;
use Chabior\Library\Lending\Domain\Entity\BookType\RestrictedBookType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Book
{
    private UuidInterface $id;

    private bool $isAvailable;

    private BookType $type;

    public function __construct(BookType $type)
    {
        $this->id = Uuid::uuid4();

        $this->isAvailable = true;
        $this->type = $type;
    }

    public static function restricted(): self
    {
        return new self(new RestrictedBookType());
    }

    public static function circulating(): self
    {
        return new self(new CirculatingBookType());
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function isRestricted(): bool
    {
        return $this->type->isRestricted();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function equals(Book $book): bool
    {
        return $this->id->equals($book->id);
    }

    public function hold(): void
    {
        $this->isAvailable = false;
    }

    public function checkout(): void
    {
        $this->isAvailable = false;
    }

    public function cancelHold(): void
    {
        $this->isAvailable = true;
    }

    public function returned(): void
    {
        $this->isAvailable = true;
    }
}
