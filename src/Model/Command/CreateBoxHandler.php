<?php

namespace App\Model\Command;

use App\Model\Box;
use App\Model\BoxRepository;

class CreateBoxHandler
{
    /**
     * @var BoxRepository
     */
    private $repository;

    public function __construct(BoxRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateBox $createBox): void
    {
        $box = Box::createWithData($createBox->id(), $createBox->amount());
        $this->repository->save($box);
    }
}