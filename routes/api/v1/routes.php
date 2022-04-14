<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\CurrencyController;
use App\Http\Controllers\Api\v1\MarketController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\WalletController;
use App\Http\Controllers\Api\v1\Gateways\CoinpaymentsController;
use App\Http\Controllers\Api\v1\Gateways\EthereumController;
use App\Http\Controllers\Api\v1\Gateways\StripeController;
use App\Http\Controllers\Api\v1\SettingController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::group(['prefix' => 'v1', 'middle', 'middleware' => ['throttle:api', 'read.only']], function () {

    Route::group(['middleware' => ['throttle:time']], function () {
        Route::get('/server-time', [SettingController::class, 'time'])->name('server-time');
    });

    Route::group(['middleware' => ['auth:sanctum', 'maintenance']], function () {

        Route::resource('currencies', CurrencyController::class);
        Route::post('/orders/cancel', [OrderController::class, 'cancel'])->name('orders.api.cancel');
        Route::get('/orders/open', [OrderController::class, 'open'])->name('orders.api.open');

        // Wallet routes
        Route::get('/wallets/getAddress', [WalletController::class, 'getAddress'])->name('wallets.api.getAddress');

        // Deposit routes
        Route::get('/wallets/deposits/{type}', [WalletController::class, 'getDeposits'])->name('wallets.api.deposits');
        Route::get('/wallets/fiat-deposits/{type?}', [WalletController::class, 'getFiatDeposits'])->name('wallets.api.deposits.fiat');

        // Withdraw routes
        Route::get('/wallets/withdrawals/{type}', [WalletController::class, 'getWithdrawals'])->name('wallets.api.withdrawals');
        Route::post('/wallets/withdraw', [WalletController::class, 'withdraw'])->name('wallets.api.withdraw');
        Route::get('/wallets/fiat-withdrawals', [WalletController::class, 'getFiatWithdrawals'])->name('wallets.api.withdrawals.fiat');

        Route::post('/wallets/payment/stripe/init', [WalletController::class, 'stripePayment'])->name('wallets.api.deposit.fiat.stripe.load');
        Route::post('/wallets/payment/stripe/validate', [WalletController::class, 'stripePaymentValidate'])->name('wallets.api.deposit.fiat.stripe.validate');

        Route::resource('wallets', WalletController::class);
    });


    Route::get('/markets/ticker', [MarketController::class, 'ticker'])->name('markets.api.ticker');
    Route::get('/markets/trades', [MarketController::class, 'trades'])->name('markets.api.trades');
    Route::get('/markets/candles/{query?}', [MarketController::class, 'candles'])->name('markets.api.candles');
    Route::get('/markets/orderbook', [MarketController::class, 'orderbook'])->name('markets.api.orderbook');

    Route::resource('markets', MarketController::class);
    Route::resource('orders', OrderController::class);

    // Coinpayments
    Route::post('/gateways/coinpayments', [CoinpaymentsController::class, 'ipn'])->name('coinpayments.ipn');

    // Stripe
    Route::post('/gateways/stripe', [StripeController::class, 'ipn'])->name('stripe.ipn');

    // Ethereum
    Route::post('/gateways/ethereum', [EthereumController::class, 'ipn'])->name('ethereum.ipn');
});
