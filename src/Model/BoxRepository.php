<?php

declare(strict_types=1);

namespace App\Model;

interface BoxRepository
{
    public function save(Box $box): void;
    public function get(string $id): ?Box;
}
