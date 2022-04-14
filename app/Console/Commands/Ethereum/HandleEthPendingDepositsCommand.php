<?php

namespace App\Console\Commands\Ethereum;

use App\Events\DepositUpdated;
use App\Mail\Deposits\AdminDepositReceived;
use App\Mail\Deposits\DepositReceived;
use App\Repositories\Deposit\DepositRepository;
use App\Repositories\Network\NetworkRepository;
use App\Repositories\Wallet\WalletRepository;
use App\Services\PaymentGateways\Coin\Ethereum\Api\EthereumGateway;
use App\Services\Wallet\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Setting;

class HandleEthPendingDepositsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ethereum:handle-eth-pending-deposits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public $depositRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->depositRepository = new DepositRepository();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $networkEth = (new NetworkRepository())->getIdBySlug('eth');
        $networkErc = (new NetworkRepository())->getIdBySlug('erc20');

        if(!$networkEth || !$networkErc) return;

        $deposits = $this->depositRepository->getByNetworks([$networkEth->id, $networkErc->id], 'pending');

        foreach ($deposits as $deposit) {

            $type = $deposit->network_id == $networkErc->id ? 'erc' : 'eth';

            $confirmations = (new EthereumGateway())->getNetworkConfirmations($deposit->txn, $type);

            if(intval($confirmations) < $deposit->currency->min_deposit_confirmation) {
                continue;
            }

            $wallet = (new WalletRepository())->getWalletByAddress($deposit->address, null, $deposit->network_id, $deposit->currency->id);

            $amount = math_sub($deposit->amount, $deposit->system_fee);

            $deposit->status = DEPOSIT_CONFIRMED;
            $deposit->confirms = intval($confirmations);
            $deposit->update();

            (new WalletService())->increase($wallet, $amount);

            event(new DepositUpdated($deposit->fresh(), 'received'));

            // Notify user
            Mail::to($wallet->user)->send(new DepositReceived($wallet->user, $amount, $wallet->currency->symbol));

            // Admin Email Notification
            $adminEmail = Setting::get('notification.admin_email', false);
            $notificationAllowed = Setting::get('notification.crypto_deposits', false);

            if($adminEmail && $notificationAllowed) {
                $route = route('admin.reports.deposits') . "?search=" . $deposit->deposit_id;
                Mail::to($adminEmail)->send(new AdminDepositReceived($amount, $wallet->currency->symbol, $route));
            }
            // END Admin Email Notification
        }
    }
}
