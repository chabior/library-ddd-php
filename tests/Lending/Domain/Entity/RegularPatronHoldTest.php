<?php declare(strict_types=1);

namespace Lending\Domain\Entity;

use Carbon\Carbon;
use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\RegularPatron;
use Chabior\Library\Lending\Domain\Event\BookHeld;
use Chabior\Library\Lending\Domain\Reason\BookIsNotAvailableReason;
use Chabior\Library\Lending\Domain\Reason\CanNotHoldForLessDayOneDayReason;
use Chabior\Library\Lending\Domain\Reason\CanNotHoldRestrictedBookReason;
use Chabior\Library\Lending\Domain\Reason\MaximumNumberOfHoldsExceededReason;
use Chabior\Library\Lending\Domain\Reason\MaximumNumberOfOverdueCheckoutsExceededReason;
use PHPUnit\Framework\TestCase;

class RegularPatronHoldTest extends TestCase
{
    public function testCanHoldCirculatingBook(): void
    {
        $book = Book::circulating();
        $patron = new RegularPatron();
        $result = $patron->hold($book, 2);

        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(BookHeld::class, $result->events()[0]);
        $this->assertFalse($book->isAvailable());
    }

    public function testCanHoldFiveBooks(): void
    {
        $patron = new RegularPatron();
        $patron->hold(Book::circulating(), 2);
        $patron->hold(Book::circulating(), 2);
        $patron->hold(Book::circulating(), 2);
        $patron->hold(Book::circulating(), 2);
        $result = $patron->hold(Book::circulating(), 2);

        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(BookHeld::class, $result->events()[0]);
    }

    public function testCanNotHoldNotAvailableBook(): void
    {
        $book = Book::circulating();
        $anotherPatron = new RegularPatron();
        $anotherPatron->hold($book, 3);

        $patron = new RegularPatron();
        $result = $patron->hold($book, 2);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(BookIsNotAvailableReason::class, $result->reason());
    }

    public function testCanNotHoldRestrictedBook(): void
    {
        $book = Book::restricted();
        $patron = new RegularPatron();
        $result = $patron->hold($book, 2);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(CanNotHoldRestrictedBookReason::class, $result->reason());
    }

    public function testCanNotHoldMoreThanFiveBooks(): void
    {
        $patron = new RegularPatron();
        $patron->hold(Book::circulating(), 2);
        $patron->hold(Book::circulating(), 2);
        $patron->hold(Book::circulating(), 2);
        $patron->hold(Book::circulating(), 2);
        $patron->hold(Book::circulating(), 2);

        $result = $patron->hold(Book::circulating(), 2);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(MaximumNumberOfHoldsExceededReason::class, $result->reason());
    }

    public function testCanNotHoldIfHasTwoOverdueCheckouts(): void
    {
        $patron = new RegularPatron();
        $book = Book::circulating();
        $patron->hold($book, 2);
        $patron->checkout($book);

        $otherBook = Book::circulating();
        $patron->hold($otherBook, 2);
        $patron->checkout($otherBook);

        //wait 60 days
        Carbon::setTestNow(Carbon::now()->endOfDay()->addDays(61));

        $result = $patron->hold(Book::circulating(), 2);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(MaximumNumberOfOverdueCheckoutsExceededReason::class, $result->reason());

        Carbon::setTestNow(Carbon::now());
    }

    public function testCanNotHoldForLessThanOneDay(): void
    {
        $book = Book::circulating();
        $patron = new RegularPatron();
        $result = $patron->hold($book, 0);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(CanNotHoldForLessDayOneDayReason::class, $result->reason());
    }
}
