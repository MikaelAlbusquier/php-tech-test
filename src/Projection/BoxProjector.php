<?php

namespace App\Projection;

use App\Model\Event\AmountAdded;
use App\Model\Event\BoxCreated;

class BoxProjector
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function onBoxCreated(BoxCreated $boxCreated): void
    {
        $query = $this->pdo->prepare('INSERT INTO `read_boxes` SET amount = ?, id = ?');
        $query->bindValue(1, $boxCreated->amount());
        $query->bindValue(2, $boxCreated->aggregateId());
        $query->execute();
    }

    public function onAmountAdded(AmountAdded $amountAdded): void
    {
        $query = $this->pdo->prepare('INSERT INTO `read_boxes` SET amount = ?, id = ?');
        $query->bindValue(1, $amountAdded->amount());
        $query->bindValue(2, $amountAdded->aggregateId());
        $query->execute();
    }
}