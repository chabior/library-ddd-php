<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

abstract class Hold
{
    private Book $book;

    private Patron $patron;

    public function __construct(Book $book, Patron $patron)
    {
        $this->book = $book;
        $this->patron = $patron;
    }

    public function isForBook(Book $book): bool
    {
        return $this->book->equals($book);
    }
}
