<?php

namespace App\Repositories\Network;

use App\Interfaces\Network\NetworkRepositoryInterface;
use App\Models\Network\Network;

class NetworkRepository implements NetworkRepositoryInterface
{
    /**
     * @var Network
     */
    protected $network;

    /**
     * NetworkRepository constructor.
     *
     */
    public function __construct()
    {
        $this->network = new Network();
    }

    public function get($onlyActive = false) {

        $network = Network::query();

        if($onlyActive) {
            $network->active();
        }

        return $network->get();
    }

    public function getById($networkId, $onlyActive = true) {
        $network = Network::query();

        if($onlyActive) {
            $network->active();
        }

        return $network->whereId($networkId)->first();
    }

    public function getIdBySlug($slug) {
        return Network::whereSlug($slug)->first();
    }

    public function update($id, $data) {
        $network = Network::find($id);
        $network->update($data);

        return $network->fresh();
    }

}
