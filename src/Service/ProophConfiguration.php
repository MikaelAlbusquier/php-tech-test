<?php

namespace App\Service;

use App\Infrastructure\BoxRepository;
use App\Model\Box;
use App\Model\Command\AddAmount;
use App\Model\Command\AddAmountHandler;
use App\Model\Command\CreateBox;
use App\Model\Command\CreateBoxHandler;
use App\Model\Event\AmountAdded;
use App\Model\Event\BoxCreated;
use App\Projection\BoxProjector;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Pdo\MySqlEventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy\MySqlAggregateStreamStrategy;
use Prooph\EventStore\Pdo\Projection\MySqlProjectionManager;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use Prooph\SnapshotStore\Pdo\PdoSnapshotStore;
use Prooph\Snapshotter\CategorySnapshotProjection;
use Prooph\Snapshotter\SnapshotReadModel;

class ProophConfiguration
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $boxId = '1';

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var BoxRepository
     */
    private $boxRepository;

    /**
     * @var BoxProjector
     */
    private $boxProjector;

    /**
     * @var ActionEventEmitterEventStore
     */
    private $eventStore;

    /**
     * @var PdoSnapshotStore
     */
    private $pdoSnapshotStore;

    /**
     * @var MySqlProjectionManager
     */
    private $projectionManager;

    /**
     * ProophConfiguration constructor.
     *
     * @param string $dbname
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct($dbname, $host, $username, $password)
    {
        $this->pdo = new \PDO(sprintf('mysql:dbname=%s;host=%s', $dbname, $host), $username, $password);
        $eventStore = new MySqlEventStore(new FQCNMessageFactory(), $this->pdo, new MySqlAggregateStreamStrategy());
        $eventEmitter = new ProophActionEventEmitter();
        $this->eventStore = new ActionEventEmitterEventStore($eventStore, $eventEmitter);

        $eventBus = new EventBus($eventEmitter);
        $eventPublisher = new EventPublisher($eventBus);
        $eventPublisher->attachToEventStore($this->eventStore);

        $this->pdoSnapshotStore = new PdoSnapshotStore($this->pdo);
        $this->boxRepository = new BoxRepository($eventStore, $this->pdoSnapshotStore);

        $this->projectionManager = new MySqlProjectionManager($eventStore, $this->pdo);

        $this->commandBus = new CommandBus();
        $router = new CommandRouter();
        $router->route(CreateBox::class)->to(new CreateBoxHandler($this->boxRepository));
        $router->route(AddAmount::class)->to(new AddAmountHandler($this->boxRepository));
        $router->attachToMessageBus($this->commandBus);

        $this->boxProjector = new BoxProjector($this->pdo);
        $eventRouter = new EventRouter();
        $eventRouter->route(BoxCreated::class)->to([$this->boxProjector, 'onBoxCreated']);
        $eventRouter->route(AmountAdded::class)->to([$this->boxProjector, 'onAmountAdded']);
        $eventRouter->attachToMessageBus($eventBus);
    }

    public function createSnapshot()
    {
        $snapshotReadModel = new SnapshotReadModel(
            $this->boxRepository,
            new AggregateTranslator(),
            $this->pdoSnapshotStore,
            [Box::class]
        );

        $projection = $this->projectionManager->createReadModelProjection(
            'box_snapshots',
            $snapshotReadModel
        );
        $categoryProjection = new CategorySnapshotProjection($projection, Box::class);
        $categoryProjection();
        $projection->run(false);
    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @return string
     */
    public function getBoxId()
    {
        return $this->boxId;
    }

    /**
     * @return CommandBus
     */
    public function getCommandBus()
    {
        return $this->commandBus;
    }

    /**
     * @return BoxRepository
     */
    public function getBoxRepository()
    {
        return $this->boxRepository;
    }

    /**
     * @return BoxProjector
     */
    public function getBoxProjector()
    {
        return $this->boxProjector;
    }

    /**
     * @return ActionEventEmitterEventStore
     */
    public function getEventStore()
    {
        return $this->eventStore;
    }

    /**
     * @return MySqlProjectionManager
     */
    public function getProjectionManager()
    {
        return $this->projectionManager;
    }
}