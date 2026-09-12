<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Blockchain Routes
|--------------------------------------------------------------------------
| Extracted from web.php for maintainability.
| Blockchain API routes use 'throttle' middleware to prevent abuse.
| Loaded inside frontend middleware group from web.php.
*/

Route::namespace('Frontend')->group(function () {
    $donation_page_slug = !empty(get_static_option('donation_page_slug')) ? get_static_option('donation_page_slug') : 'donations';

    Route::get('/blockchain-donate/{id}', 'BlockchainPaymentController@showDonationForm')->name('blockchain.donate.form');
    Route::post('/blockchain-donate/process', 'BlockchainPaymentController@processDonation')->name('blockchain.donate.process');
    Route::get('/blockchain/transaction/{hash}', 'BlockchainPaymentController@transactionSuccess')->name('blockchain.transaction.success');
    Route::get('/blockchain/tx/{hash}', 'BlockchainPaymentController@showTransaction')->name('blockchain.transaction.show');
    Route::get('/blockchain/verify/{hash}', 'BlockchainPaymentController@verifyTransaction')->name('blockchain.transaction.verify');
    Route::get('/blockchain/explorer', 'BlockchainPaymentController@explorer')->name('blockchain.explorer');
    Route::get('/donation/receipt/{track}', 'BlockchainPaymentController@receipt')->name('donation.receipt');
});

/*----------------------------------
    BLOCKCHAIN API ROUTES
----------------------------------*/
Route::prefix('api/blockchain')->group(function () {
    Route::post('/connect-wallet', 'Api\BlockchainApiController@connectWallet')
        ->middleware('throttle:30,1')
        ->name('api.blockchain.connect-wallet');
    Route::get('/wallet-status', 'Api\BlockchainApiController@walletStatus')
        ->middleware('throttle:60,1')
        ->name('api.blockchain.wallet-status');
    Route::post('/disconnect-wallet', 'Api\BlockchainApiController@disconnectWallet')
        ->middleware('throttle:10,1')
        ->name('api.blockchain.disconnect-wallet');
    Route::post('/auth/nonce', 'Auth\WalletAuthController@nonce')
        ->middleware('throttle:10,1')
        ->name('api.blockchain.auth.nonce');
    Route::post('/auth/verify', 'Auth\WalletAuthController@verify')
        ->middleware('throttle:10,1')
        ->name('api.blockchain.auth.verify');
    Route::post('/donate', 'Api\BlockchainApiController@processDonation')
        ->middleware('throttle:5,1')
        ->name('api.blockchain.donate');
    Route::post('/verify-transaction', 'Api\BlockchainApiController@verifyTransaction')
        ->middleware('throttle:20,1')
        ->name('api.blockchain.verify');
    Route::get('/campaign/{id}', 'Api\BlockchainApiController@campaignData')
        ->middleware('throttle:60,1')
        ->name('api.blockchain.campaign-data');
    Route::get('/network-stats', 'Api\BlockchainApiController@networkStats')
        ->middleware('throttle:30,1')
        ->name('api.blockchain.network-stats');
    Route::post('/release-escrow', 'Api\BlockchainApiController@releaseEscrow')
        ->middleware('throttle:5,1')
        ->name('api.blockchain.release-escrow');
    Route::get('/config', 'Admin\AdminBlockchainController@config')
        ->middleware('throttle:60,1')
        ->name('api.blockchain.config');
});

/*----------------------------------
    PUBLIC TRANSPARENCY JSON API
----------------------------------*/
Route::prefix('api')->group(function () {
    Route::get('/campaigns', 'Api\PublicApiController@campaigns')
        ->middleware('throttle:60,1')
        ->name('api.public.campaigns');
    Route::get('/campaigns/{id}', 'Api\PublicApiController@campaign')->where('id', '[0-9]+')
        ->middleware('throttle:60,1')
        ->name('api.public.campaign');
    Route::get('/tx/{hash}', 'Api\PublicApiController@transaction')->where('hash', '[a-fA-F0-9x]+')
        ->middleware('throttle:30,1')
        ->name('api.public.tx');
    Route::get('/docs', function () {
        return view('frontend.api-docs');
    })->middleware('throttle:10,1')->name('api.docs');
});
