<?php declare(strict_types=1);

namespace Lending\Domain\Entity;

use Carbon\Carbon;
use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\Researcher;
use Chabior\Library\Lending\Domain\Event\BookHoled;
use Chabior\Library\Lending\Domain\Reason\BookIsNotAvailableReason;
use Chabior\Library\Lending\Domain\Reason\MaximumNumberOfOverdueCheckoutsExceededReason;
use PHPUnit\Framework\TestCase;

class ResearcherHoldTest extends TestCase
{
    public function testCanHoldRestrictedBook(): void
    {
        $researcher = new Researcher();
        $book = Book::restricted();
        $result = $researcher->hold($book);

        $this->assertTrue($result->isSuccess());
        $this->assertFalse($book->isAvailable());
        $this->assertInstanceOf(BookHoled::class, $result->events()[0]);
    }

    public function testCanHoldCirculatingBook(): void
    {
        $researcher = new Researcher();
        $book = Book::circulating();
        $result = $researcher->hold($book);

        $this->assertTrue($result->isSuccess());
        $this->assertFalse($book->isAvailable());
        $this->assertInstanceOf(BookHoled::class, $result->events()[0]);
    }

    public function testCanNotHoldNotAvailableBook(): void
    {
        $book = Book::circulating();
        $otherResearcher = new Researcher();
        $otherResearcher->hold($book);

        $researcher = new Researcher();
        $result = $researcher->hold($book);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(BookIsNotAvailableReason::class, $result->reason());
    }

    public function testCanNotHoldIfHasTwoOverdueCheckouts(): void
    {
        $researcher = new Researcher();
        $book = Book::circulating();
        $researcher->hold($book);
        $researcher->checkout($book);

        $otherBook = Book::restricted();
        $researcher->hold($otherBook);
        $researcher->checkout($otherBook);

        //wait 60 days
        Carbon::setTestNow(Carbon::now()->addDays(60));

        $result = $researcher->hold(Book::circulating());

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(MaximumNumberOfOverdueCheckoutsExceededReason::class, $result->reason());

        Carbon::setTestNow(Carbon::now());
    }
}
