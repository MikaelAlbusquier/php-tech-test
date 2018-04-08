<?php

declare(strict_types=1);

namespace App\Model\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;

class AddAmount extends Command
{
    use PayloadTrait;

    public function id(): string
    {
        return $this->payload()['id'];
    }

    public function amount(): string
    {
        return $this->payload()['amount'];
    }
}