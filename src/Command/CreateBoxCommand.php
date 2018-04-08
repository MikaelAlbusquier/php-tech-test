<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Command\CreateBox;
use App\Service\ProophConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateBoxCommand extends Command
{
    /**
     * @var ProophConfiguration
     */
    private $proophConfiguration;

    public function __construct(ProophConfiguration $proophConfiguration)
    {
        $this->proophConfiguration = $proophConfiguration;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('app:create-box')
            ->setDescription('Creates a new box.')
            ->setHelp('This command allows you to create a box')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->proophConfiguration->getCommandBus()->dispatch(new CreateBox([
            'id' => $this->proophConfiguration->getBoxId(),
            'amount' => '0'
        ]));
    }
}