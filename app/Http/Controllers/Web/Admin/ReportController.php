<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit\FiatDeposit;
use App\Models\Withdrawal\FiatWithdrawal;
use App\Models\Withdrawal\Withdrawal;
use App\Repositories\Currency\CurrencyRepository;
use App\Repositories\Deposit\DepositRepository;
use App\Repositories\Deposit\FiatDepositRepository;
use App\Repositories\Transaction\ReferralTransactionRepository;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\Wallet\WalletRepository;
use App\Repositories\Withdrawal\FiatWithdrawalRepository;
use App\Repositories\Withdrawal\WithdrawalRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Setting;

class ReportController extends Controller
{
    public function index() {
        return Redirect::route('admin.reports.wallets.system');
    }

    public function deposits() {

        $depositRepository = new DepositRepository();

        $deposits = $depositRepository->getReport();

        return Inertia::render('Admin/Reports/Deposits', [
            'filters' => request()->all(['search', 'type', 'referrer']),
            'deposits' => $deposits,
        ]);
    }


    public function fiatDeposits() {

        $depositRepository = new FiatDepositRepository();

        $deposits = $depositRepository->getReport();

        return Inertia::render('Admin/Reports/FiatDeposits', [
            'filters' => request()->all(['search', 'type', 'referrer']),
            'deposits' => $deposits,
        ]);
    }

    public function withdrawals() {

        $withdrawalRepository = new WithdrawalRepository();

        $withdrawals = $withdrawalRepository->getReport();

        return Inertia::render('Admin/Reports/Withdrawals', [
            'filters' => request()->all(['search', 'type', 'referrer']),
            'withdrawals' => $withdrawals,
        ]);
    }

    public function fiatWithdrawals() {

        $withdrawalRepository = new FiatWithdrawalRepository();

        $withdrawals = $withdrawalRepository->getReport();

        return Inertia::render('Admin/Reports/FiatWithdrawals', [
            'filters' => request()->all(['search', 'referrer']),
            'withdrawals' => $withdrawals,
        ]);
    }

    public function trades() {

        $transactionRepository = new TransactionRepository();

        $transactions = $transactionRepository->getReport();

        return Inertia::render('Admin/Reports/Trades', [
            'filters' => request()->all(['search', 'type', 'referrer']),
            'transactions' => $transactions,
        ]);
    }

    public function wallets() {

        $walletRepository = new WalletRepository();

        $wallets = $walletRepository->getReport();

        $search = request()->get('search');
        $type = request()->get('type', 'all');
        $referrer = request()->get('referrer');

        return Inertia::render('Admin/Reports/Wallets', [
            'filters' => [
                'search' => $search,
                'type' => $type,
                'referrer' => $referrer
            ],
            'wallets' => $wallets,
        ]);
    }

    public function systemWallets() {

        $currencyRepository = new CurrencyRepository();

        $requests = request()->only(['search','type']);

        $currencies = $currencyRepository->getReport($requests);

        $search = request()->get('search');
        $type = request()->get('type', 'all');
        $referrer = request()->get('referrer');

        return Inertia::render('Admin/Reports/SystemWallets', [
            'filters' => [
                'search' => $search,
                'type' => $type,
                'referrer' => $referrer
            ],
            'currencies' => $currencies,
        ]);
    }

    public function referralTransactions() {

        $transactionRepository = new ReferralTransactionRepository();

        $transactions = $transactionRepository->getReport();

        return Inertia::render('Admin/Reports/ReferralTransactions', [
            'filters' => request()->all(['search', 'referrer']),
            'transactions' => $transactions,
        ]);
    }

    /**
     * Moderate withdrawal action
     *
     * @return \Illuminate\Http\Response
     */
    public function moderateWithdrawal(Request $request, Withdrawal $withdrawal)
    {
        $result = (new WithdrawalRepository())->moderate($withdrawal, $request->get('action'));

        return Redirect::route('admin.reports.withdrawals');
    }

    /**
     * Moderate Fiat Deposit
     */
    public function moderateFiatDeposit(Request $request, FiatDeposit $deposit) {

        $result = (new FiatDepositRepository())->moderate($deposit, $request->get('action'));

        return Redirect::route('admin.reports.deposits.fiat');
    }

    /**
     * Moderate Fiat Withdrawal
     */
    public function moderateFiatWithdrawal(Request $request, FiatWithdrawal $withdrawal) {

        $result = (new FiatWithdrawalRepository())->moderate($withdrawal, $request->get('action'));

        return Redirect::route('admin.reports.withdrawals.fiat');
    }
}
