<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Model\Box;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\EventStore;
use Prooph\SnapshotStore\SnapshotStore;
use App\Model\BoxRepository as BaseBoxRepository;

class BoxRepository extends AggregateRepository implements BaseBoxRepository
{
    public function __construct(EventStore $eventStore, SnapshotStore $snapshotStore)
    {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass(Box::class),
            new AggregateTranslator(),
            $snapshotStore,
            null,
            true
        );
    }

    public function save(Box $box): void
    {
        $this->saveAggregateRoot($box);
    }

    public function get(string $id): ?Box
    {
        return $this->getAggregateRoot($id);
    }
}