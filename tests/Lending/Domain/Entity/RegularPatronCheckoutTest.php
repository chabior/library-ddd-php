<?php declare(strict_types=1);

namespace Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\RegularPatron;
use Chabior\Library\Lending\Domain\Event\BookCheckout;
use Chabior\Library\Lending\Domain\Reason\CanNotCheckoutNotHeldBookReason;
use PHPUnit\Framework\TestCase;

class RegularPatronCheckoutTest extends TestCase
{
    public function testCanCheckoutHoledBook(): void
    {
        $patron = new RegularPatron();
        $book = Book::circulating();
        $patron->hold($book, 1);

        $result = $patron->checkout($book);
        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(BookCheckout::class, $result->events()[0]);
    }

    public function testCanNotCheckoutNotHoledBook(): void
    {
        $patron = new RegularPatron();
        $book = Book::circulating();

        $result = $patron->checkout($book);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(CanNotCheckoutNotHeldBookReason::class, $result->reason());
    }
}
