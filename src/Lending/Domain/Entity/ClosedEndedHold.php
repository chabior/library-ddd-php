<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Carbon\Carbon;
use DateTimeInterface;

class ClosedEndedHold extends Hold
{
    private DateTimeInterface $expiresAt;

    public function __construct(Book $book, RegularPatron $regularPatron, int $days)
    {
        parent::__construct($book, $regularPatron);

        $this->expiresAt = Carbon::now()->endOfDay()->addDays($days);
    }
}
