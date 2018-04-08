<?php

declare(strict_types=1);

namespace App\Model\Command;

use App\Model\Box;
use App\Model\BoxRepository;

class AddAmountHandler
{
    /**
     * @var BoxRepository
     */
    private $repository;

    public function __construct(BoxRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(AddAmount $addAmount): void
    {
        /* @var Box $box */
        $box = $this->repository->get($addAmount->id());
        $box->addAmount($addAmount->amount());
        $this->repository->save($box);
    }
}