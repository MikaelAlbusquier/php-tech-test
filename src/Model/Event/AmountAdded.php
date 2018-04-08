<?php

declare(strict_types=1);

namespace App\Model\Event;

use Prooph\EventSourcing\AggregateChanged;

class AmountAdded extends AggregateChanged
{
    public function amount(): string
    {
        return $this->payload['amount'];
    }
}