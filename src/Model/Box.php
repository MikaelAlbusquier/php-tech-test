<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Event\AmountAdded;
use App\Model\Event\BoxCreated;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

class Box extends AggregateRoot
{
    private $id;
    private $amount;

    public function addAmount($amount): void
    {
        $this->recordThat(AmountAdded::occur($this->id, [
            'amount' => $amount
        ]));
    }

    public function createWithData(string $id, string $amount): self
    {
        $obj = new self;
        $obj->recordThat(BoxCreated::occur($id, [
            'amount' => $amount
        ]));

        return $obj;
    }

    protected function aggregateId(): string
    {
        return $this->id;
    }

    protected function apply(AggregateChanged $event): void
    {
        switch (get_class($event)) {
            case BoxCreated::class:
                /** @var BoxCreated $event */
                $this->id = $event->aggregateId();
                $this->amount = intval($event->amount());
                break;
            case AmountAdded::class:
                /** @var AmountAdded $event */
                $this->id = $event->aggregateId();
                $this->amount += intval($event->amount());
                break;
        }
    }

    public function getAmount()
    {
        return $this->amount;
    }
}
