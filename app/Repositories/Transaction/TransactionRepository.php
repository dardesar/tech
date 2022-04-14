<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction\Transaction;
use App\Models\User\User;

class TransactionRepository
{
    public function count() {

        $transaction = Transaction::query();

        $transaction->maker();

        return $transaction->count();
    }

    public function getReport() {

        $transaction = Transaction::query();

        $transaction->filter(request()->only(['search']))->orderByLatest();

        $transaction->with(['market.quoteCurrency', 'market.baseCurrency', 'user']);

        return $transaction->paginate(50)->withQueryString();
    }

    public function getReportUser(User $user) {

        $transaction = Transaction::query();

        $transaction->filterUser(request()->only(['market', 'side']))->orderByLatest();

        $transaction->with(['market.quoteCurrency', 'market.baseCurrency']);

        //$transaction->where('user_id', $user->id);

        return $transaction->paginate(50)->withQueryString();
    }
}
