<?php declare(strict_types=1);

namespace Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\Researcher;
use Chabior\Library\Lending\Domain\Event\BookReturned;
use Chabior\Library\Lending\Domain\Reason\CanNotReturnNotCheckoutBookReason;
use PHPUnit\Framework\TestCase;

class ResearcherReturnBookTest extends TestCase
{
    public function testCanReturnCheckoutBook(): void
    {
        $book = Book::restricted();
        $researcher = new Researcher();
        $researcher->hold($book, null);
        $researcher->checkout($book);

        $result = $researcher->returnBook($book);

        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(BookReturned::class, $result->events()[0]);
    }

    public function testCanNotReturnNotCheckoutBook(): void
    {
        $book = Book::restricted();
        $researcher = new Researcher();
        $researcher->hold($book, null);

        $result = $researcher->returnBook($book);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(CanNotReturnNotCheckoutBookReason::class, $result->reason());
    }
}
