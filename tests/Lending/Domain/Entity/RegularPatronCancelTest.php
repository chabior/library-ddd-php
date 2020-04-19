<?php declare(strict_types=1);

namespace Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\RegularPatron;
use Chabior\Library\Lending\Domain\Event\HoldCanceled;
use Chabior\Library\Lending\Domain\Reason\CanNotCancelNotHeldBookReason;
use PHPUnit\Framework\TestCase;

class RegularPatronCancelTest extends TestCase
{
    public function testCanCancelHeldBook(): void
    {
        $patron = new RegularPatron();
        $book = Book::circulating();
        $patron->hold($book, 1);

        $result = $patron->cancelHold($book);
        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(HoldCanceled::class, $result->events()[0]);
    }

    public function testCanNotCancelNotHeldBook(): void
    {
        $patron = new RegularPatron();
        $book = Book::restricted();

        $result = $patron->cancelHold($book);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(CanNotCancelNotHeldBookReason::class, $result->reason());
    }
}
