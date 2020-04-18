<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use DateTimeInterface;

class OpenEndedHold
{
    private Book $book;

    private Researcher $researcher;

    public function __construct(Book $book, Researcher $researcher)
    {
        $this->book = $book;
        $this->researcher = $researcher;
    }

    public function isForBook(Book $book): bool
    {
        return $this->book->equals($book);
    }
}
