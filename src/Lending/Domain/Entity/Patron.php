<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Chabior\Library\Common\Result;
use Chabior\Library\Lending\Domain\Event\BookCheckout;
use Chabior\Library\Lending\Domain\Event\BookHeld;
use Chabior\Library\Lending\Domain\Event\BookReturned;
use Chabior\Library\Lending\Domain\Event\HoldCanceled;
use Chabior\Library\Lending\Domain\Policy\HoldPolicy;
use Chabior\Library\Lending\Domain\Reason\CanNotCancelNotHeldBookReason;
use Chabior\Library\Lending\Domain\Reason\CanNotCheckoutNotHeldBookReason;
use Chabior\Library\Lending\Domain\Reason\CanNotReturnNotCheckoutBookReason;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

abstract class Patron
{
    private UuidInterface $id;
    private Collection $holds;
    private Collection $checkouts;

    public function __construct()
    {
        $this->holds = new ArrayCollection();
        $this->checkouts = new ArrayCollection();
    }

    abstract protected function createHoldPolicy(): HoldPolicy;
    abstract protected function createHold(Book $book, ?int $numberOfDays): Hold;

    public function hold(Book $book, ?int $numberOfDays): Result
    {
        $holdPolicy = $this->createHoldPolicy();
        if (!$holdPolicy->isFulfilled($this, $book, $numberOfDays)) {
            return Result::failure($holdPolicy->reason());
        }

        $this->holds->add($this->createHold($book, $numberOfDays));
        $book->hold();

        return Result::success(new BookHeld($book->getId(), $numberOfDays));
    }

    public function checkout(Book $book): Result
    {
        $hold = $this->findHold($book);

        if ($hold === null) {
            return Result::failure(new CanNotCheckoutNotHeldBookReason());
        }

        $this->holds->removeElement($book);
        $this->checkouts->add(new Checkout($this, $book));
        $book->hold();

        return Result::success(new BookCheckout());
    }

    public function cancelHold(Book $book): Result
    {
        $hold = $this->findHold($book);

        if ($hold === null) {
            return Result::failure(new CanNotCancelNotHeldBookReason());
        }

        $this->holds->removeElement($book);
        $book->cancelHold();

        return Result::success(new HoldCanceled());
    }

    public function returnBook(Book $book): Result
    {
        $checkout = $this->findCheckout($book);

        if ($checkout === null) {
            return Result::failure(new CanNotReturnNotCheckoutBookReason());
        }

        $this->checkouts->removeElement($book);
        $book->returned();

        return Result::success(new BookReturned());
    }

    public function hasMoreOrEqualsHolds(int $value): bool
    {
        return $this->holds->count() >= $value;
    }

    public function hasMoreOrEqualsOverdueCheckouts(int $value): bool
    {
        return $this->getNumberOfOverdueCheckouts() >= $value;
    }

    private function findHold(Book $book): ?Hold
    {
        $hold = $this->holds->filter(static function (Hold $hold) use ($book): bool {
            return $hold->isForBook($book);
        })->first();

        return $hold !== false ? $hold : null;
    }

    private function findCheckout(Book $book): ?Checkout
    {
        $checkout = $this->checkouts->filter(static function (Checkout $checkout) use ($book): bool {
            return $checkout->isForBook($book);
        })->first();

        return $checkout !== false ? $checkout : null;
    }

    private function getNumberOfOverdueCheckouts(): int
    {
        return $this->checkouts->filter(
            static function (Checkout $checkout): bool {
                return $checkout->isOverdue();
            }
        )->count();
    }
}
