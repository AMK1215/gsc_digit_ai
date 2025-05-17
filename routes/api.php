<?php

use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Bank\BankController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\Game\DirectLaunchGameController;
use App\Http\Controllers\Api\V1\Game\LaunchGameController;
use App\Http\Controllers\Api\V1\NewVersion\PlaceBetWebhookController;
use App\Http\Controllers\Api\V1\Player\DepositController;
use App\Http\Controllers\Api\V1\Player\PlayerTransactionLogController;
use App\Http\Controllers\Api\V1\Player\TransactionController;
use App\Http\Controllers\Api\V1\Player\UserPaymentControler;
use App\Http\Controllers\Api\V1\Player\WagerController;
use App\Http\Controllers\Api\V1\Player\WithDrawController;
use App\Http\Controllers\Api\V1\PromotionController;
use App\Http\Controllers\Api\V1\Slot\GameController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\BonusController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\BuyInController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\BuyOutController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\CancelBetController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\GameResultController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\GetBalanceController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\GetGameListController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\JackPotController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\MobileLoginController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\PlaceBetController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\PushBetController;
use App\Http\Controllers\Api\V1\Webhook\Gsc\RollbackController;
use App\Models\Admin\Role;
use Illuminate\Support\Facades\Route;

//auth api
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('v1/validate', [AuthController::class, 'callback']);
Route::get('gameTypeProducts/{id}', [GameController::class, 'gameTypeProducts']);
Route::get('allGameProducts', [GameController::class, 'allGameProducts']);

Route::delete('/game-lists-delete', [GameController::class, 'deleteGameLists']);

Route::post('/operatorgetgamelist', [GetGameListController::class, 'getGameList']);
// for slot
Route::group(['prefix' => 'Seamless'], function () {
    Route::post('GetBalance', [GetBalanceController::class, 'getBalance']);
    Route::post('PlaceBet', [PlaceBetController::class, 'placeBet']);
    Route::post('GameResult', [GameResultController::class, 'gameResult']);
    Route::post('Rollback', [RollbackController::class, 'rollback']);
    // // Route::group(["middleware" => ["webhook_log"]], function(){
    // // Route::post('GetGameList', [LaunchGameController::class, 'getGameList']);
    Route::post('CancelBet', [CancelBetController::class, 'cancelBet']);
    Route::post('BuyIn', [BuyInController::class, 'buyIn']);
    Route::post('BuyOut', [BuyOutController::class, 'buyOut']);
    Route::post('PushBet', [PushBetController::class, 'pushBet']);
    Route::post('Bonus', [BonusController::class, 'bonus']);
    Route::post('Jackpot', [JackPotController::class, 'jackPot']);
    Route::post('MobileLogin', [MobileLoginController::class, 'MobileLogin']);
    // });
});

Route::group(['middleware' => ['auth:sanctum', 'playerBannedCheck']], function () {

    //games api
    Route::get('game_types', [GameController::class, 'gameType']);
    Route::get('providers/{id}', [GameController::class, 'gameTypeProducts']);
    Route::get('game_lists/{product_id}/{game_type_id}', action: [GameController::class, 'gameList']);
    Route::get('hot_games', [GameController::class, 'HotgameList']);
    Route::get('wager-logs', [WagerController::class, 'index']);
    Route::get('transactions', [TransactionController::class, 'index']);

    //auth api
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('changePassword', [AuthController::class, 'changePassword']);
    Route::post('profile', [AuthController::class, 'profile']);
    Route::post('updateProfile', [AuthController::class, 'updateProfile']);

    //common api
    Route::get('banners', [BannerController::class, 'index']);
    Route::get('banner_text', [BannerController::class, 'bannerText']);
    Route::get('ads_banner', [BannerController::class, 'AdsBannerIndex']);
    Route::get('promotions', [PromotionController::class, 'index']);
    Route::get('contacts', [ContactController::class, 'contact']);
    Route::get('banks', [BankController::class, 'banks']);
    Route::get('bonus-log', [BankController::class, 'bonusLog']);

    Route::group(['prefix' => 'transaction'], function () {
        Route::post('withdraw', [WithDrawController::class, 'withdraw']);
        Route::post('deposit', [DepositController::class, 'deposit']);
        Route::get('player-transactionlog', [PlayerTransactionLogController::class, 'index']);
        Route::get('deposit-log', [TransactionController::class, 'depositRequestLog']);
        Route::get('withdraw-log', [TransactionController::class, 'withDrawRequestLog']);
    });

    Route::group(['prefix' => 'game'], function () {
        Route::post('Seamless/LaunchGame', [LaunchGameController::class, 'launchGame']);
        //Route::get('gamelist/{provider_id}/{game_type_id}', [GameController::class, 'gameList']);
    });

    Route::group(['prefix' => 'direct'], function () {
        Route::post('Seamless/LaunchGame', [DirectLaunchGameController::class, 'launchGame']);
    });

});

Route::get('/game/gamelist/{provider_id}/{game_type_id}', [GameController::class, 'gameList']);
