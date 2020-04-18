<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Chabior\Library\Common\Result;
use Chabior\Library\Lending\Domain\Event\BookCheckout;
use Chabior\Library\Lending\Domain\Event\BookHoled;
use Chabior\Library\Lending\Domain\Event\BookReturned;
use Chabior\Library\Lending\Domain\Event\HoldCanceled;
use Chabior\Library\Lending\Domain\Reason\BookIsNotAvailableReason;
use Chabior\Library\Lending\Domain\Reason\CanNotCancelNotHoledBookReason;
use Chabior\Library\Lending\Domain\Reason\CanNotCheckoutNotHoledBookReason;
use Chabior\Library\Lending\Domain\Reason\CanNotReturnNotCheckoutBookReason;
use Chabior\Library\Lending\Domain\Reason\MaximumNumberOfOverdueCheckoutsExceededReason;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Researcher
{
    private Collection $holds;
    private Collection $checkouts;

    public function __construct()
    {
        $this->holds = new ArrayCollection();
        $this->checkouts = new ArrayCollection();
    }

    public function hold(Book $book): Result
    {
        if (!$book->isAvailable()) {
            return Result::failure(new BookIsNotAvailableReason());
        }

        if ($this->getNumberOfOverdueCheckouts() > 2) {
            return Result::failure(new MaximumNumberOfOverdueCheckoutsExceededReason());
        }

        $this->holds->add(new OpenEndedHold($book, $this));
        $book->hold();

        return Result::success(new BookHoled($book->getId(), null));
    }

    public function checkout(Book $book): Result
    {
        $hold = $this->findHold($book);

        if ($hold === null) {
            return Result::failure(new CanNotCheckoutNotHoledBookReason());
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
            return Result::failure(new CanNotCancelNotHoledBookReason());
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

    private function findHold(Book $book): ?OpenEndedHold
    {
        $hold = $this->holds->filter(static function (OpenEndedHold $hold) use ($book): bool {
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
