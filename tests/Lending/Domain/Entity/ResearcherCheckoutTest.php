<?php declare(strict_types=1);

namespace Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\Researcher;
use Chabior\Library\Lending\Domain\Event\BookCheckout;
use Chabior\Library\Lending\Domain\Reason\CanNotCheckoutNotHeldBookReason;
use PHPUnit\Framework\TestCase;

class ResearcherCheckoutTest extends TestCase
{
    public function testCanCheckoutHoledBook(): void
    {
        $researcher = new Researcher();
        $book = Book::restricted();
        $researcher->hold($book, null);

        $result = $researcher->checkout($book);
        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(BookCheckout::class, $result->events()[0]);
    }

    public function testCanNotCheckoutNotHoledBook(): void
    {
        $researcher = new Researcher();
        $book = Book::restricted();

        $result = $researcher->checkout($book);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(CanNotCheckoutNotHeldBookReason::class, $result->reason());
    }
}
