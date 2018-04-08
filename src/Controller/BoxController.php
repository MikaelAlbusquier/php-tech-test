<?php

namespace App\Controller;

use App\Model\Box;
use App\Model\Command\AddAmount;
use App\Service\ProophConfiguration;
use Prooph\EventStore\Metadata\MetadataMatcher;
use Prooph\EventStore\Metadata\Operator;
use Prooph\EventStore\StreamName;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BoxController
{
    public function add(Request $request, ProophConfiguration $proophConfiguration)
    {
        $amount = $request->get('amount');

        $proophConfiguration->getCommandBus()->dispatch(new AddAmount([
            'id' => $proophConfiguration->getBoxId(),
            'amount' => $amount
        ]));

        return new Response('Amount Added!');
    }

    public function total(ProophConfiguration $proophConfiguration)
    {
        $boxId = $proophConfiguration->getBoxId();

        /* @var Box $box */
        $box = $proophConfiguration->getBoxRepository()->get($boxId);

        return new Response(json_encode([
            'total' => $box->getAmount()
        ]));
    }

    public function history(ProophConfiguration $proophConfiguration)
    {
        $sql = 'select * from `_c1496ae84b332893ad096d9b5ad3459c3af64925` where event_name like "%\AmountAdded"';

        $stmt = $proophConfiguration->getPdo()->prepare($sql);

        $stmt->execute();

        $results = $stmt->fetchAll();

        $response = [];

        foreach ($results as $result) {
            $decoded = json_decode($result['payload'], true);
            $response[] = [
                'amount' => $decoded['amount'],
                'created_at' => $result['created_at']
            ];
        }

        return new Response(json_encode($response));
    }
}