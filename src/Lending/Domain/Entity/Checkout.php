<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Carbon\Carbon;
use DateTimeInterface;

class Checkout
{
    private Patron $patron;

    private Book $book;

    private DateTimeInterface $expiresAt;

    public function __construct(Patron $patron, Book $book)
    {
        $this->patron = $patron;
        $this->book = $book;
        $this->expiresAt = Carbon::now()->addDays(60);
    }

    public function isForBook(Book $book): bool
    {
        return $this->book->equals($book);
    }

    public function isOverdue(): bool
    {
        return Carbon::now()->greaterThan($this->expiresAt);
    }


}
