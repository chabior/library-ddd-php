<?php declare(strict_types=1);

namespace Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\RegularPatron;
use Chabior\Library\Lending\Domain\Event\BookReturned;
use Chabior\Library\Lending\Domain\Reason\CanNotReturnNotCheckoutBookReason;
use PHPUnit\Framework\TestCase;

class RegularPatronReturnBookTest extends TestCase
{
    public function testCanReturnCheckoutBook(): void
    {
        $book = Book::circulating();
        $patron = new RegularPatron();
        $patron->hold($book, 1);
        $patron->checkout($book);

        $result = $patron->returnBook($book);

        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(BookReturned::class, $result->events()[0]);
    }

    public function testCanNotReturnNotCheckoutBook(): void
    {
        $book = Book::circulating();
        $researcher = new RegularPatron();
        $researcher->hold($book, 1);

        $result = $researcher->returnBook($book);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(CanNotReturnNotCheckoutBookReason::class, $result->reason());
    }
}
