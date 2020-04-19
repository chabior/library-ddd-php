<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

class OpenEndedHold extends Hold
{
    public function __construct(Book $book, Researcher $patron)
    {
        parent::__construct($book, $patron);
    }
}
