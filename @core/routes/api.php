<?php

use App\Http\Controllers\Admin\CausesController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\HomeController;
use \App\Http\Controllers\Api\UserCampaignController;
use App\Http\Controllers\Api\LanguageController;

Route::group(['prefix'=> 'v1' ,'middleware' => ['setlang:frontend'] ],function(){

    Route::get("/get-currency-symbol",function (){
        $data = ["symbol" => site_currency_symbol()];
        return response()->json($data);
    });

        //todo: languages
        Route::get('/language',[LanguageController::class,'languageInfo']);
        Route::post('/translate-string',[LanguageController::class,'translateString'])->middleware('setlang:frontend');

       // Login Register Routes
        Route::post('/register',[UserController::class,'register']);
        Route::post('/login',[UserController::class,'login']);
        Route::post('/send-otp-in-mail',[UserController::class,'sendOTP']);
        Route::post('/reset-password',[UserController::class,'resetPassword']);
        Route::get("/country-list",[UserController::class,"country_list"]);

      //Frontend Routes
        Route::get("/slider",[HomeController::class,"all_slider"]);
        Route::get("/campaign-details/{id}",[HomeController::class,"donation_details"]);
        Route::get("/people-who-donated/{cause_id}",[HomeController::class,"people_who_donated"]);
        Route::get("/related-campaigns/{cause_id}",[HomeController::class,"related_campaigns"]);
        Route::get("/currencies",[HomeController::class,"currencies"]);
        Route::get("/language-rtl-support",[HomeController::class,"language_rtl_support"]);
        Route::get("/custom-amounts",[HomeController::class,"custom_amounts"]);
        Route::get("/payment-gateway-list",[\App\Http\Controllers\Api\PaymentGatewayController::class,"gateway_list"]);
        Route::get("/event-list",[HomeController::class,"event_list"]);
        Route::get("/event-single/{id}",[HomeController::class,"event_single"]);
        Route::get("/campaign-categories",[HomeController::class,"campaign_categories"]);
        Route::get("/campaign-by-category/{id}/{any?}",[HomeController::class,"campaign_by_category"]);
        Route::post("/social-login",[HomeController::class,"social_login"]);
        Route::get("/donation-admin-settings",[HomeController::class,"donation_admin_settings"]);
        

        /* Donation */
        Route::group(['prefix' => 'donation'],function(){
            Route::get('/',[HomeController::class,'multiple_donation_data']);
        });

  //=============================== USER DASHBOARD ROUTES =============================
     Route::group(['prefix' => 'user/','middleware' => 'auth:sanctum'],function (){
        Route::get('dashboard',[UserController::class,'dashboard']);
        Route::post('logout',[UserController::class,'logout']);
        Route::get('profile',[UserController::class,'profile']);
        Route::post('change-password',[UserController::class,'changePassword']);
        Route::post('update-profile',[UserController::class,'updateProfile']);

        //User Donations
        Route::group(['prefix' => 'donation'],function(){
            Route::get('/',[UserController::class,'user_donations']);
            Route::post('/user-follow-store',[UserController::class,'user_follow_store']);
            Route::get('/followed-user',[UserController::class,'followed_user_campaigns']);
            Route::get('/followed-user-campaign/{id}',[UserController::class,'followed_user_campaigns_list']);
            Route::get('/reward-points',[UserController::class,'reward_points']);
            Route::get('/reward-redeem-available-amount/{user_id}',[UserController::class,'reward_redeem_available_amount']);
            Route::post('/reward-redeem-submit',[UserController::class,'reward_redeem_submit']);
            Route::post('/pay',[\App\Http\Controllers\Api\DonationPaymentController::class,'pay']);
            Route::get('/payment-status-update/{id}',[\App\Http\Controllers\Api\DonationPaymentController::class,'payment_status_update']);
            Route::post('/comment-store',[UserController::class,'comment_store']);

        });

         //Event Attendance
         Route::group(['prefix' => 'event'],function(){
             Route::get('/booked-event',[UserController::class,'user_event_booking']);
             Route::get('/event-order/cancel/{id}', [UserController::class,'event_order_cancel']);
             Route::post('/pay',[\App\Http\Controllers\Api\EventPaymentController::class,'pay']);
             Route::get('/payment-status-update/{id}',[\App\Http\Controllers\Api\EventPaymentController::class,'payment_status_update']);
         });

        //Support Tickets
        Route::group(['prefix' => 'support-tickets'],function(){
            Route::get('/',[UserController::class,'allTickets']);
            Route::post('/{id}',[UserController::class,'viewTickets']);
            Route::get("/ticket-departments",[UserController::class,"all_ticket_department"]);
        });

        //Support Ticket Routes

        Route::get("/ticket",[UserController::class,"get_all_tickets"]);
        Route::get("/ticket/{id}",[UserController::class,"single_ticket"]);
        Route::get("/ticket/chat/{ticket_id}",[UserController::class,"fetch_support_chat"]);
        Route::post("/ticket/chat/send/{ticket_id}",[UserController::class,"send_support_chat"]);

        Route::post('ticket/message-send',[UserController::class,'sendMessage']);
        Route::post('/send-otp-in-mail/success',[UserController::class,'sendOTPSuccess']);
        Route::post('ticket/create',[UserController::class,'createTicket']);
        Route::get('check-followed/{id}',[UserController::class,'check_followed']);
        
         //User Campaign
         Route::group(['prefix' => 'user-campaign'],function(){
             Route::get('/',[UserCampaignController::class,'user_created_campaign_list']);
             Route::post('/store', [UserCampaignController::class,'user_created_campaign_store']);
             Route::post('/update/{id}', [UserCampaignController::class,'user_created_campaign_update']);
             Route::get('/delete/{id}', [UserCampaignController::class,'user_created_campaign_delete']);
             Route::get('/campaign-permission', [UserCampaignController::class,'user_campaign_permission']);
         });
        
    });

});

Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});

