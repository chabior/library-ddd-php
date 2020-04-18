<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeInterface;

class Checkout
{
    private Researcher $researcher;

    private Book $book;

    private DateTimeInterface $expiresAt;

    public function __construct(Researcher $researcher, Book $book)
    {
        $this->researcher = $researcher;
        $this->book = $book;
        $this->expiresAt = Carbon::now()->addDays(60);
    }

    public function isForBook(Book $book): bool
    {
        return $this->book->equals($book);
    }

    public function isOverdue(): bool
    {
        return $this->expiresAt < new DateTimeImmutable();
    }


}
