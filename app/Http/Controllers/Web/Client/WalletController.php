<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Wallet\FiatDepositFormRequest;
use App\Http\Requests\Web\Wallet\FiatWithdrawFormRequest;
use App\Http\Resources\Country\CountryCollection;
use App\Http\Resources\Currency\Currency;
use App\Http\Resources\Currency\FiatCurrency;
use App\Mail\Deposits\AdminDepositReceived;
use App\Repositories\Country\CountryRepository;
use App\Repositories\Currency\CurrencyRepository;
use App\Repositories\Deposit\FiatDepositRepository;
use App\Repositories\Withdrawal\FiatWithdrawalRepository;
use App\Services\Currency\CurrencyService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Setting;

class WalletController extends Controller
{
    /**
     * @var currencyService
     */
    protected $currencyService;

    /**
     * WalletController Constructor
     *
     * @param CurrencyService $currencyService
     *
     */
    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Wallet/Wallets');
    }

    /**
     * Display deposit screen
     *
     * @return \Illuminate\Http\Response
     */
    public function depositCrypto($symbol = '')
    {
        if(!$symbol) {
            abort(404);
        }

        $currency = $this->currencyService->getCurrencyBySymbol($symbol, 'coin', true, ['networks', 'file']);

        if(!$currency) {
            abort(404);
        }

        return Inertia::render('Wallet/Deposit/DepositCrypto', [
            'symbol' => $symbol,
            'currency' => new Currency($currency),
        ]);
    }

    /**
     * Display withdraw screen
     *
     * @return \Illuminate\Http\Response
     */
    public function withdrawCrypto($symbol = '')
    {
        $currency = $this->currencyService->getCurrencyBySymbol($symbol);

        return Inertia::render('Wallet/Withdraw/WithdrawCrypto', [
            'symbol' => $symbol,
            'currency' => new Currency($currency)
        ]);
    }

    /**
     * Display deposit screen
     *
     * @return \Illuminate\Http\Response
     */
    public function depositFiat($symbol = '')
    {
        if(!$symbol) {
            abort(404);
        }

        $currency = $this->currencyService->getCurrencyBySymbol($symbol, 'fiat', true, ['networks','bankAccount.country']);

        if(!$currency) {
            abort(404);
        }

        return Inertia::render('Wallet/Deposit/DepositFiat', [
            'symbol' => $symbol,
            'currency' => new FiatCurrency($currency),
            'stripe' => setting('stripe.public_key'),
        ]);
    }

    /**
     * Display deposit screen
     *
     * @return \Illuminate\Http\Response
     */
    public function depositFiatSuccess()
    {
        return Inertia::render('Wallet/Deposit/Fiat/DepositFiatSuccess');
    }

    /**
     * Display deposit screen
     *
     * @return \Illuminate\Http\Response
     */
    public function withdrawFiatSuccess()
    {
        return Inertia::render('Wallet/Withdraw/Fiat/WithdrawFiatSuccess');
    }

    /**
     * Display deposit screen
     *
     * @return \Illuminate\Http\Response
     */
    public function withdrawCryptoSuccess()
    {
        return Inertia::render('Wallet/Withdraw/Coin/WithdrawCryptoSuccess');
    }

    /**
     * Display withdraw screen
     *
     * @return \Illuminate\Http\Response
     */
    public function withdrawFiat($symbol = '')
    {
        $currency = $this->currencyService->getCurrencyBySymbol($symbol);

        if(!$currency) {
            abort(404);
        }

        $countries = new CountryCollection((new CountryRepository())->get());

        return Inertia::render('Wallet/Withdraw/WithdrawFiat', [
            'symbol' => $symbol,
            'currency' => new FiatCurrency($currency),
            'countries' => $countries
        ]);
    }

    /**
     * Store the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function depositStore(FiatDepositFormRequest $request)
    {
        $data = $request->only([
            'amount',
            'currency_id',
            'receipt_id',
            'note'
        ]);

        $currency = (new CurrencyRepository())->get($request->get('currency_id'), false, false, []);

        $fee = math_percentage($request->get('amount'), $currency->deposit_fee);

        $data['user_id'] = auth()->user()->getAuthIdentifier();
        $data['status'] = FIAT_DEPOSIT_PENDING;
        $data['type'] = 'bank';
        $data['deposit_id'] = generate_uuid();
        $data['fee'] = $fee;

        (new FiatDepositRepository())->store($data);

        // Admin Email Notification
        $adminEmail = Setting::get('notification.admin_email', false);
        $notificationAllowed = Setting::get('notification.fiat_deposits', false);

        if($adminEmail && $notificationAllowed) {
            $route = route('admin.reports.deposits.fiat') . "?search=" . $data['deposit_id'];
            Mail::to($adminEmail)->send(new AdminDepositReceived(math_formatter($request->get('amount'), $currency->decimals), $currency->symbol, $route));
        }
        // END Admin Email Notification

        return Redirect::route('wallets.deposit.fiat', $currency->symbol);
    }

    /**
     * Store the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function withdrawStore(FiatWithdrawFormRequest $request)
    {
        $data = $request->only([
            'name',
            'iban',
            'swift',
            'ifsc',
            'address',
            'account_holder_name',
            'account_holder_address',
            'country_id',
            'amount',
            'currency_id',
        ]);

        $currency = (new CurrencyRepository())->get($request->get('currency_id'), false, false, []);

        $fee = math_percentage($request->get('amount'), $currency->withdraw_fee);

        $data['user_id'] = auth()->user()->getAuthIdentifier();
        $data['status'] = FIAT_WITHDRAWAL_PENDING;
        $data['withdrawal_id'] = generate_uuid();
        $data['fee'] = $fee;

        (new FiatWithdrawalRepository())->processWithdraw($data, $currency);

        return Redirect::route('wallets.withdraw.fiat', $currency->symbol);
    }

}
