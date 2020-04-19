<?php declare(strict_types=1);

namespace Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Entity\Book;
use Chabior\Library\Lending\Domain\Entity\Researcher;
use Chabior\Library\Lending\Domain\Event\HoldCanceled;
use Chabior\Library\Lending\Domain\Reason\CanNotCancelNotHeldBookReason;
use PHPUnit\Framework\TestCase;

class ResearcherCancelTest extends TestCase
{
    public function testCanCancelHeldBook(): void
    {
        $researcher = new Researcher();
        $book = Book::restricted();
        $researcher->hold($book, null);

        $result = $researcher->cancelHold($book);
        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(HoldCanceled::class, $result->events()[0]);
    }

    public function testCanNotCancelNotHeldBook(): void
    {
        $researcher = new Researcher();
        $book = Book::restricted();

        $result = $researcher->cancelHold($book);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(CanNotCancelNotHeldBookReason::class, $result->reason());
    }
}
