<?php declare(strict_types=1);

namespace Chabior\Library\Lending\Domain\Entity;

use Chabior\Library\Lending\Domain\Entity\BookType\RestrictedBookType;

abstract class BookType
{
    public function isRestricted(): bool
    {
        return get_class($this) === RestrictedBookType::class;
    }
}
