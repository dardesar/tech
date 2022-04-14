<?php

namespace App\Repositories\Deposit;

use App\Interfaces\Deposit\DepositRepositoryInterface;
use App\Models\Deposit\Deposit;
use App\Models\User\User;

class DepositRepository implements DepositRepositoryInterface
{
    /**
     * @var Deposit
     */
    protected $deposit;

    /**
     * DepositRepository constructor.
     *
     */
    public function __construct()
    {
        $this->deposit = new Deposit();
    }

    public function get() {

        $deposit = Deposit::query();

        $deposit->with('currency.file');

        $deposit->orderBy('created_at', 'desc');

        $deposit->limit(10);

        return $deposit->get();
    }

    public function getReport() {

        $deposit = Deposit::query();

        $deposit->filter(request()->only(['search', 'type']))->orderByLatest();

        $deposit->with(['currency', 'network', 'user']);

        return $deposit->paginate(50)->withQueryString();
    }

    public function getReportUser(User $user, $pagination = true) {

        $deposit = Deposit::query();

        $deposit->filterUser(request()->only(['currency','txn','status']))->orderByLatest();

        $deposit->with(['currency.file']);

        $deposit->where('user_id', $user->id);

        if(!$pagination) {
            $deposit->limit(15);
            return $deposit->get();
        }

        return $deposit->paginate(50)->withQueryString();
    }

    public function count() {

        $deposit = Deposit::query();

        return $deposit->count();
    }

    public function getDeposit($id) {
        return Deposit::with('currency')->whereId($id)->first();
    }

    public function store($data) {
        return $this->deposit->create($data);
    }

    public function update($deposit, $data) {
        return $deposit->update($data);
    }

    public function getBySource($source_id, $network) {
        return Deposit::with('currency')->where('source_id', $source_id)->where('network_id', $network)->first();
    }

    public function getByNetwork($network, $status = 'pending') {
        return Deposit::with('currency')->whereStatus($status)->where('network_id', $network)->get();
    }

    public function getByNetworks($networks, $status = 'pending') {

        return Deposit::with('currency')->whereStatus($status)->where(function ($query) use ($networks) {
            $query->where('network_id', $networks[0]);
            $query->orWhere('network_id', $networks[1]);
        })->get();
    }
}
