<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Frontend Only)
|--------------------------------------------------------------------------
| Admin routes -> routes/admin.php
| Blockchain routes -> routes/blockchain.php
| Admin blockchain/fraud/verification routes -> routes/admin.php
*/

Route::feeds();
Route::post('/subscribe-newsletter','FrontendController@subscribe_newsletter')->name('frontend.subscribe.newsletter')->middleware('setlang:frontend');

Route::group(['middleware' =>['setlang:frontend','globalVariable','maintains_mode']],function (){

    Route::get('/lang','FrontendController@lang_change')->name('frontend.langchange');

    /*----------------------------------------------------------------
     | JOBS FRONTEND
     |--------------------------------------------------------------*/
    Route::group(['middleware' => 'globalVariable','namespace'=>'Frontend'], function () {
        $career_with_us_page_slug = get_static_option('career_with_us_page_slug') ?? 'jobs';
        Route::get('/' . $career_with_us_page_slug, 'FrontendJobController@jobs')->name('frontend.jobs');
        Route::get('/' . $career_with_us_page_slug . '/{slug}', 'FrontendJobController@jobs_single')->name('frontend.jobs.single');
        Route::get('/' . $career_with_us_page_slug . '-category/{id}/{any?}', 'FrontendJobController@jobs_category')->name('frontend.jobs.category');
        Route::get('/' . $career_with_us_page_slug . '-search', 'FrontendJobController@jobs_search')->name('frontend.jobs.search');
        Route::get('/'.$career_with_us_page_slug.'/apply/{id}','FrontendJobController@jobs_apply')->name('frontend.jobs.apply');
        Route::get('/job-success/{id}','FrontendJobController@job_payment_success')->name('frontend.job.payment.success');
        Route::get('/job-cancel/{id}','FrontendJobController@job_payment_cancel')->name('frontend.job.payment.cancel');

        Route::post('/apply', 'JobPaymentController@store_jobs_applicant_data')->name('frontend.jobs.apply.store');
        Route::get('/job-paypal-ipn','JobPaymentController@paypal_ipn')->name('frontend.job.paypal.ipn');
        Route::post('/job-paytm-ipn','JobPaymentController@paytm_ipn')->name('frontend.job.paytm.ipn');
        Route::get('/job-stripe-ipn','JobPaymentController@stripe_ipn')->name('frontend.job.stripe.ipn');
        Route::post('/job-razorpay-ipn','JobPaymentController@razorpay_ipn')->name('frontend.job.razorpay.ipn');
        Route::get('/job-mollie-ipn','JobPaymentController@mollie_ipn')->name('frontend.job.mollie.ipn');
        Route::get('/job-flutterwave-ipn','JobPaymentController@flutterwave_ipn')->name('frontend.job.flutterwave.ipn');
        Route::get('/job-midtrans-ipn','JobPaymentController@midtrans_ipn')->name('frontend.job.midtrans.ipn');
        Route::post('/job-payfast-ipn','JobPaymentController@payfast_ipn')->name('frontend.job.payfast.ipn');
        Route::post('/job-cashfree-ipn','JobPaymentController@cashfree_ipn')->name('frontend.job.cashfree.ipn');
        Route::get('/job-instamojo-ipn','JobPaymentController@instamojo_ipn')->name('frontend.job.instamojo.ipn');
        Route::get('/job-marcadopago-ipn','JobPaymentController@marcadopago_ipn')->name('frontend.job.marcadopago.ipn');
        Route::get('/job-squreup-ipn','JobPaymentController@squreup_ipn')->name('frontend.job.squreup.ipn');
        Route::post('/job-cinetpay-ipn','JobPaymentController@cinetpay_ipn')->name('frontend.job.cinetpay.ipn');
        Route::post('/job-paytabs-ipn','JobPaymentController@paytabs_ipn')->name('frontend.job.paytabs.ipn');
        Route::post('/job-billplz-ipn','JobPaymentController@billplz_ipn')->name('frontend.job.billplz.ipn');
        Route::post('/job-toyyibpay-ipn','JobPaymentController@toyyibpay_ipn')->name('frontend.job.toyyibpay.ipn');
        Route::post('/job-pagali-ipn','JobPaymentController@pagali_ipn')->name('frontend.job.pagali.ipn');
        Route::post('/job-sitesway-ipn','JobPaymentController@sitesway_ipn')->name('frontend.job.sitesway.ipn');
        Route::get('/job-authorizenet-ipn','JobPaymentController@authorizenet_ipn')->name('frontend.job.authorizenet.ipn');
    });

    /*----------------------------------------------------------------
     | EVENTS PAYMENTS
     |--------------------------------------------------------------*/
    Route::group(['middleware' => 'globalVariable','namespace'=>'Frontend'], function (){
        $events_page_slug = get_static_option('events_page_slug') ?? 'events';
        Route::get('/'.$events_page_slug,'FrontendEventController@events')->name('frontend.events');
        Route::get('/'.$events_page_slug.'/{slug}','FrontendEventController@events_single')->name('frontend.events.single');
        Route::get('/'.$events_page_slug.'-category/{id}/{any?}','FrontendEventController@events_category')->name('frontend.events.category');
        Route::get('/'.$events_page_slug.'-search','FrontendEventController@events_search')->name('frontend.events.search');
        Route::get('/'.$events_page_slug.'-booking/{id}','FrontendEventController@event_booking')->name('frontend.event.booking');
        Route::get('/event-flullterwave/pay','FrontendEventController@flutterwave_pay_get')->name('frontend.event.flutterwave.pay');
        Route::get('/booking-confirm/{id}','FrontendEventController@booking_confirm')->name('frontend.event.booking.confirm');
        Route::get('/attendance-success/{id}','FrontendEventController@event_payment_success')->name('frontend.event.payment.success');
        Route::get('/attendance-cancel/{id}','FrontendEventController@event_payment_cancel')->name('frontend.event.payment.cancel');

        Route::get('/event-paypal-ipn','EventPaymentLogsController@paypal_ipn')->name('frontend.event.paypal.ipn');
        Route::post('/event-paytm-ipn','EventPaymentLogsController@paytm_ipn')->name('frontend.event.paytm.ipn');
        Route::get('/event-stripe-ipn','EventPaymentLogsController@stripe_ipn')->name('frontend.event.stripe.ipn');
        Route::post('/event-razorpay-ipn','EventPaymentLogsController@razorpay_ipn')->name('frontend.event.razorpay.ipn');
        Route::get('/paystack-ipn','EventPaymentLogsController@paystack_ipn')->name('frontend.event.paystack.ipn');
        Route::get('/event-flullterwave-ipn','EventPaymentLogsController@flutterwave_ipn')->name('frontend.event.flutterwave.ipn');
        Route::get('/event-event-mollie-ipn','EventPaymentLogsController@mollie_ipn')->name('frontend.event.mollie.ipn');
        Route::get('/event-midtrans-ipn','EventPaymentLogsController@midtrans_ipn')->name('frontend.event.midtrans.ipn');
        Route::post('/event-payfast-ipn','EventPaymentLogsController@payfast_ipn')->name('frontend.event.payfast.ipn');
        Route::post('/event-cashfree-ipn','EventPaymentLogsController@cashfree_ipn')->name('frontend.event.cashfree.ipn');
        Route::get('/event-instamojo-ipn','EventPaymentLogsController@instamojo_ipn')->name('frontend.event.instamojo.ipn');
        Route::get('/event-marcadopago-ipn','EventPaymentLogsController@marcadopago_ipn')->name('frontend.event.marcadopago.ipn');
        Route::get('/event-squreup-ipn','EventPaymentLogsController@squreup_ipn')->name('frontend.event.squreup.ipn');
        Route::post('/event-cinetpay-ipn','EventPaymentLogsController@cinetpay_ipn')->name('frontend.event.cinetpay.ipn');
        Route::post('/event-paytabs-ipn','EventPaymentLogsController@paytabs_ipn')->name('frontend.event.paytabs.ipn');
        Route::post('/event-billplz-ipn','EventPaymentLogsController@billplz_ipn')->name('frontend.event.billplz.ipn');
        Route::post('/event-toyyibpay-ipn','EventPaymentLogsController@toyyibpay_ipn')->name('frontend.event.toyyibpay.ipn');
        Route::post('/event-pagali-ipn','EventPaymentLogsController@pagali_ipn')->name('frontend.event.pagali.ipn');
        Route::post('/event-sitesway-ipn','EventPaymentLogsController@sitesway_ipn')->name('frontend.event.sitesway.ipn');
        Route::get('/event-authorizenet-ipn','EventPaymentLogsController@authorizenet_ipn')->name('frontend.event.authorizenet.ipn');

        Route::post('/booking-confirm', 'EventPaymentLogsController@booking_payment_form')->name('frontend.event.payment.confirm');
        Route::post('/event-user/generate-invoice','FrontendEventController@generate_event_invoice')->name('frontend.event.invoice.generate');
    });

    /*----------------------------------------------------------------
     | CAUSES FRONTEND ROUTES
     |--------------------------------------------------------------*/
    Route::namespace('Frontend')->group(function (){
        $donation_page_slug = !empty(get_static_option('donation_page_slug')) ? get_static_option('donation_page_slug') : 'donations';

        Route::get('/'.$donation_page_slug.'/payment/donate/{id}','FrontendCausesController@redirect_to_blockchain_donation')->name('frontend.donation.in.separate.page');
        Route::get('/'.$donation_page_slug.'/payment/recurring-separate/{token}','FrontendCausesController@donations_recuring_separate')->name('frontend.donation.recurring.separate.page');
        Route::get('/'.$donation_page_slug,'FrontendCausesController@donations')->name('frontend.donations');
        Route::get('/'.$donation_page_slug.'/{slug}','FrontendCausesController@donations_single')->name('frontend.donations.single');
        Route::post('/donation-user/generate-invoice','FrontendCausesController@generate_donation_invoice')->name('frontend.donation.invoice.generate');
        Route::post('/load/donor/data','FrontendCausesController@load_donor_data')->name('frontend.load.cause.donor.data');
        Route::post('/load/donation-update/data','FrontendCausesController@load_donation_update_data')->name('frontend.load.cause.donation.update.data');
        Route::get('/'.$donation_page_slug.'-cat/{id}/{any?}','FrontendCausesController@donation_by_category')->name('frontend.donations.category');
        Route::get('/'.$donation_page_slug.'-search','FrontendCausesController@donation_search_page')->name('frontend.donation.search');

        Route::post('/'.$donation_page_slug.'/comment/store','FrontendCausesController@cause_comment_store')->name('cause.comment.store');
        Route::post('/'.$donation_page_slug.'/all/comment','FrontendCausesController@cause_all_comment')->name('cause.all.comment');
        Route::post('/'.$donation_page_slug.'/load/cause/comment/data','FrontendCausesController@load_cause_comment_data')->name('frontend.load.cause.comment.data');
        Route::post('/'.$donation_page_slug.'/get/donation/charges/by/ajax','FrontendCausesController@get_donation_charges_by_ajax')->name('frontend.get.donation.charges.by.ajax');
        Route::post('/'.$donation_page_slug.'/flag/report/store','FrontendCausesController@flag_report_store')->name('frontend.donation.flag.report.store');
        Route::post('/'.$donation_page_slug,'CausesLogController@store_donation_logs')->name('frontend.donations.log.store');
        Route::get('/'.$donation_page_slug.'/gift/checkout/{id}/{d_id}','FrontendCausesController@gift_checkout')->name('frontend.donation.gift.checkout');

        Route::get('/donation-paypal-ipn','CausesLogController@paypal_ipn')->name('frontend.donation.paypal.ipn');
        Route::post('/donation-paytm-ipn','CausesLogController@paytm_ipn')->name('frontend.donation.paytm.ipn');
        Route::get('/donation-stripe-ipn','CausesLogController@stripe_ipn')->name('frontend.donation.stripe.ipn');
        Route::post('/donation-razorpay-ipn','CausesLogController@razorpay_ipn')->name('frontend.donation.razorpay.ipn');
        Route::get('/donation-mollie-ipn','CausesLogController@mollie_ipn')->name('frontend.donation.mollie.ipn');
        Route::get('/donation-flutterwave-ipn','CausesLogController@flutterwave_ipn')->name('frontend.donation.flutterwave.ipn');
        Route::get('/donation-midtrans-ipn','CausesLogController@midtrans_ipn')->name('frontend.donation.midtrans.ipn');
        Route::post('/donation-payfast-ipn','CausesLogController@payfast_ipn')->name('frontend.donation.payfast.ipn');
        Route::post('/donation-cashfree-ipn','CausesLogController@cashfree_ipn')->name('frontend.donation.cashfree.ipn');
        Route::get('/donation-instamojo-ipn','CausesLogController@instamojo_ipn')->name('frontend.donation.instamojo.ipn');
        Route::get('/donation-marcadopago-ipn','CausesLogController@marcadopago_ipn')->name('frontend.donation.marcadopago.ipn');
        Route::get('/donation-squreup-ipn','CausesLogController@squreup_ipn')->name('frontend.donation.squreup.ipn');
        Route::post('/donation-cinetpay-ipn','CausesLogController@cinetpay_ipn')->name('frontend.donation.cinetpay.ipn');
        Route::post('/donation-paytabs-ipn','CausesLogController@paytabs_ipn')->name('frontend.donation.paytabs.ipn');
        Route::post('/donation-billplz-ipn','CausesLogController@billplz_ipn')->name('frontend.donation.billplz.ipn');
        Route::post('/donation-zitopay-ipn','CausesLogController@zitopay_ipn')->name('frontend.donation.zitopay.ipn');
        Route::post('/donation-toyyibpay-ipn','CausesLogController@toyyibpay_ipn')->name('frontend.donation.toyyibpay.ipn');
        Route::post('/donation-pagali-ipn','CausesLogController@pagali_ipn')->name('frontend.donation.pagali.ipn');
        Route::post('/donation-sitesway-ipn','CausesLogController@sitesway_ipn')->name('frontend.donation.sitesway.ipn');
        Route::get('/donation-authorizenet-ipn','CausesLogController@authorizenet_ipn')->name('frontend.donation.authorizenet.ipn');
        Route::get('/donation-success/{id}','FrontendCausesController@donation_payment_success')->name('frontend.donation.payment.success');
        Route::get('/donation-cancel','FrontendCausesController@donation_payment_cancel')->name('frontend.donation.payment.cancel');
        Route::get($donation_page_slug.'-by-{user}/{id}','FrontendCausesController@user_created_donations')->name('frontend.user.created.donations');
    });

    /*----------------------------------
        FRONTEND: SUPPORT TICKET ROUTES
    ----------------------------------*/
    Route::group(['namespace' => 'Frontend'], function () {
        $support_ticket_page_slug = get_static_option('support_ticket_page_slug') ?? 'support-ticket';
        Route::get($support_ticket_page_slug, 'SupportTicketController@page')->name('frontend.support.ticket');
        Route::post($support_ticket_page_slug.'/new', 'SupportTicketController@store')->name('frontend.support.ticket.store');
    });

    /*------------------------------
        SOCIAL LOGIN CALLBACK
    ------------------------------*/
    Route::group(['prefix' => 'facebook'], function (){
        Route::get('callback','SocialLoginController@facebook_callback')->name('facebook.callback');
        Route::get('redirect','SocialLoginController@facebook_redirect')->name('login.facebook.redirect');
    });
    Route::group(['prefix' => 'google'], function (){
        Route::get('callback','SocialLoginController@google_callback')->name('google.callback');
        Route::get('redirect','SocialLoginController@google_redirect')->name('login.google.redirect');
    });

    /*------------------------------
       STATIC PAGES ROUTES
    ------------------------------*/
    $about_page_slug = !empty(get_static_option('about_page_slug')) ? get_static_option('about_page_slug') : 'about';
    $faq_page_slug = !empty(get_static_option('faq_page_slug')) ? get_static_option('faq_page_slug') : 'faq';
    $team_page_slug = !empty(get_static_option('team_page_slug')) ? get_static_option('team_page_slug') : 'team';
    $contact_page_slug = !empty(get_static_option('contact_page_slug')) ? get_static_option('contact_page_slug') : 'contact';
    $blog_page_slug = !empty(get_static_option('blog_page_slug')) ? get_static_option('blog_page_slug') : 'blog';
    $testimonial_page_slug = !empty(get_static_option('testimonial_page_slug')) ? get_static_option('testimonial_page_slug') : 'testimonials';
    $image_gallery_page_slug = !empty(get_static_option('image_gallery_page_slug')) ? get_static_option('image_gallery_page_slug') : 'image-gallery';
    $donor_page_slug = !empty(get_static_option('donor_page_slug')) ? get_static_option('donor_page_slug') : 'donor-list';
    $success_story_page_slug = !empty(get_static_option('success_story_page_slug')) ? get_static_option('success_story_page_slug') : 'success-story';

    Route::get('/','FrontendController@index')->name('homepage');
    Route::get('/p/{slug?}/{id}','FrontendController@dynamic_single_page')->name('frontend.dynamic.page');
    Route::get('/home/{id}','FrontendController@home_page_change')->name('homepage.demo');

    Route::get('/'.$donor_page_slug,'FrontendController@donor_list')->name('frontend.donor.list');
    Route::get('/'.$about_page_slug,'FrontendController@about_page')->name('frontend.about');
    Route::get('/'.$image_gallery_page_slug,'FrontendController@image_gallery_page')->name('frontend.image.gallery');
    Route::get('/'.$faq_page_slug,'FrontendController@faq_page')->name('frontend.faq');
    Route::get('/'.$team_page_slug,'FrontendController@team_page')->name('frontend.team');
    Route::get('/'.$testimonial_page_slug,'FrontendController@testimonials')->name('frontend.testimonials');
    Route::get('/'.$contact_page_slug,'FrontendController@contact_page')->name('frontend.contact');

    Route::get('/'.$success_story_page_slug,'FrontendController@success_story_page')->name('frontend.success.story');
    Route::get('/'.$success_story_page_slug.'/{slug}','FrontendController@success_story_single')->name('frontend.success.story.single');
    Route::get('/'.$success_story_page_slug.'-category/{id}/{any?}','FrontendController@success_story_category')->name('frontend.success.story.category');

    Route::get('/subscriber/email-verify/{token}','FrontendController@subscriber_verify')->name('subscriber.verify');
    Route::get('/newsletter/unsubscribe','FrontendController@newsletter_unsubscribe')->name('frontend.newsletter.unsubscribe');

    $events_page_slug = get_static_option('events_page_slug') ?? 'events';
    Route::post('/'.$events_page_slug.'-booking','FrontendFormController@store_event_booking_data')->name('frontend.event.booking.store');

    Route::post('/contact-message','FrontendFormController@send_contact_message')->name('frontend.contact.message');

    Route::get('/paypal-ipn', 'PaymentLogController@paypal_ipn')->name('frontend.paypal.ipn');
    Route::post('/paytm-ipn', 'PaymentLogController@paytm_ipn')->name('frontend.paytm.ipn');
    Route::post('/stripe','PaymentLogController@stripe_charge')->name('frontend.stripe.charge');
    Route::get('/stripe/pay','PaymentLogController@stripe_ipn')->name('frontend.stripe.ipn');
    Route::post('/razorpay', 'PaymentLogController@razorpay_ipn')->name('frontend.razorpay.ipn');
    Route::post('/paystack/pay', 'PaymentLogController@paystack_pay')->name('frontend.paystack.pay');
    Route::get('/paystack/callback', 'PaymentLogController@paystack_callback')->name('frontend.paystack.ipn');
    Route::get('/flutterwave/callback', 'PaymentLogController@flutterwave_callback')->name('frontend.flutterwave.callback');
    Route::get('/mollie/callback', 'PaymentLogController@mollie_webhook')->name('frontend.mollie.webhook');

    /*----------------------------------------------------------------
     | BLOG FRONTEND ROUTES
     |--------------------------------------------------------------*/
    Route::get('/'.$blog_page_slug,'FrontendController@blog_page')->name('frontend.blog');
    Route::get('/'.$blog_page_slug.'/{slug}','FrontendController@blog_single_page')->name('frontend.blog.single');
    Route::get('/'.$blog_page_slug.'-search','FrontendController@blog_search_page')->name('frontend.blog.search');
    Route::get('/'.$blog_page_slug.'-category/{id}/{any?}','FrontendController@category_wise_blog_page')->name('frontend.blog.category');
    Route::get('/'.$blog_page_slug.'-tags/{name}','FrontendController@tags_wise_blog_page')->name('frontend.blog.tags.page');

    /*----------------------------------------------------------------
     | USER DASHBOARD
     |--------------------------------------------------------------*/
    Route::get('campaign/user', 'FrontendController@user_campaign')->name('frontend.campaign.user');

    Route::prefix('user-home')->middleware(['userEmailVerify', 'setlang:frontend', 'globalVariable', 'maintains_mode'])->group(function () {
        Route::get('/', 'UserDashboardController@user_index')->name('user.home');
        Route::post('/wallet/connect', 'UserDashboardController@connect_wallet')->name('user.wallet.connect');
        Route::post('/wallet/disconnect', 'UserDashboardController@disconnect_wallet')->name('user.wallet.disconnect');
        Route::get('/download/file/{id}', 'UserDashboardController@download_file')->name('user.dashboard.download.file');
        Route::get('/events-booking', 'UserDashboardController@event_booking')->name('user.home.event.booking');
        Route::get('/donations', 'UserDashboardController@donations')->name('user.home.donations');

        Route::get('/change-password', 'UserDashboardController@change_password')->name('user.home.change.password');
        Route::get('/edit-profile', 'UserDashboardController@edit_profile')->name('user.home.edit.profile');
        Route::post('/profile-update', 'UserDashboardController@user_profile_update')->name('user.profile.update');
        Route::post('/password-change', 'UserDashboardController@user_password_change')->name('user.password.change');
        Route::post('/event-order/cancel', 'UserDashboardController@event_order_cancel')->name('user.dashboard.event.order.cancel');
        Route::post('/donation-order/cancel', 'UserDashboardController@donation_order_cancel')->name('user.dashboard.donation.order.cancel');
        Route::get('/support-tickets', 'UserDashboardController@support_tickets')->name('user.home.support.tickets');
        Route::get('support-ticket/view/{id}', 'UserDashboardController@support_ticket_view')->name('user.dashboard.support.ticket.view');
        Route::post('support-ticket/priority-change', 'UserDashboardController@support_ticket_priority_change')->name('user.dashboard.support.ticket.priority.change');
        Route::post('support-ticket/status-change', 'UserDashboardController@support_ticket_status_change')->name('user.dashboard.support.ticket.status.change');
        Route::post('support-ticket/message', 'UserDashboardController@support_ticket_message')->name('user.dashboard.support.ticket.message');
        Route::get('/tax-information', 'UserDashboardController@tax_page')->name('user.home.tax.information');
        Route::post('/tax-information/update', 'UserDashboardController@tax_information_update')->name('user.home.tax.information.update');
        Route::get('/user-verify', 'UserDashboardController@user_verify')->name('user.home.verify.update');
        Route::post('/user-verify', 'UserDashboardController@update_user_verify');
        Route::get('/request-log', 'UserDashboardController@tax_request_log')->name('user.home.tax.request.log');
        Route::post('/request-log-store', 'UserDashboardController@tax_request_store')->name('user.home.tax.request.store');
        Route::post('/user-follow-store', 'UserDashboardController@user_follow_store')->name('user.home.user.follow.store');
        Route::get('/reward-points', 'UserDashboardController@reward_points')->name('user.home.reward.point');
        Route::post('/reward/redeem/check','UserDashboardController@reward_redeem_check')->name('user.reward.redeem.check');
        Route::post('/reward/redeem/submit','UserDashboardController@reward_redeem_submit')->name('user.reward.redeem.submit');
        Route::get('/reward-points/redeem-logs', 'UserDashboardController@reward_redeem_logs')->name('user.home.reward.redeem.log');
        Route::get('/reward/redeem/view/{id}','UserDashboardController@reward_redeem_view')->name('user.reward.redeem.view');

        Route::group(['namespace' => 'User'], function (){
            Route::get('/all/campaign', 'UserCampaignController@all_campaign')->name('user.campaign.all');
            Route::get('campaign/new', 'UserCampaignController@new_campaign')->name('user.campaign.new');
            Route::post('campaign/new', 'UserCampaignController@store_campaign');
            Route::get('campaign/edit/{id}', 'UserCampaignController@edit_campaign')->name('user.campaign.edit');
            Route::post('/campaign/update', 'UserCampaignController@update_campaign')->name('user.campaign.update');
            Route::post('/campaign/delete/{id}', 'UserCampaignController@delete_campaign')->name('user.campaign.delete');
        });

        Route::group(['namespace' => 'User'], function (){
            Route::get('/hospital-verifications', 'HospitalDashboardController@index')->name('user.hospital.dashboard');
            Route::post('/hospital-verifications/verify/{id}', 'HospitalDashboardController@verify')->name('user.hospital.verify');
            Route::post('/hospital-verifications/reject/{id}', 'HospitalDashboardController@reject')->name('user.hospital.reject');
        });

        Route::group(['namespace' => 'User'], function (){
            Route::get('/all-campaign-gifts', 'CausesGiftController@all_donation_gift')->name('user.campaign.gift.all');
            Route::get('campaign-gifts-new', 'CausesGiftController@create_donation_gift')->name('user.campaign.gift.new');
            Route::post('campaign-gifts-store', 'CausesGiftController@store_donation_gift')->name('user.campaign.gift.store');
            Route::get('campaign-gift-edit/{id}', 'CausesGiftController@edit_donation_gift')->name('user.campaign.gift.edit');
            Route::post('/campaign-gifts-update', 'CausesGiftController@update_donation_gift')->name('user.campaign.gift.update');
            Route::post('/campaign-gifts-delete/{id}', 'CausesGiftController@delete_donation_gift')->name('user.campaign.gift.delete');
        });

        Route::get('/all/cause/update/{id}', 'UserDashboardController@user_all_update_causes')->name('user.all.update.cause.page');
        Route::get('/new/cause/update/{id}', 'UserDashboardController@new_user_update_cause')->name('user.add.new.update.cause.page');
        Route::post('/new/cause/update/{id}', 'UserDashboardController@store_update_causes');
        Route::post('/update/cause/update', 'UserDashboardController@update_update_causes')->name('user.donations.update.cause.update');
        Route::post('/delete/cause/update/{id}', 'UserDashboardController@delete_update_cause')->name('user.donations.update.cause.delete');
        Route::get('/campaign/log/withdraw','UserDashboardController@campaign_log_withdraw')->name('user.campaign.log.withdraw');
        Route::post('/campaign/withdraw/submit','UserDashboardController@campaign_withdraw_submit')->name('user.campaign.withdraw.submit');
        Route::post('/campaign/withdraw/check','UserDashboardController@campaign_withdraw_check')->name('user.campaign.withdraw.check');
        Route::get('/campaign/withdraw/view/{id}','UserDashboardController@campaign_withdraw_view')->name('user.campaign.withdraw.view');
        Route::get('/following/user-campaign','UserDashboardController@following_user_campaigns')->name('following.user.campaigns');

        Route::group(['prefix'=>'media-upload','namespace'=>'User'], function () {
            Route::post('/', 'MediaUploadController@upload_media_file')->name('user.upload.media.file');
            Route::post('/all', 'MediaUploadController@all_upload_media_file')->name('user.upload.media.file.all');
            Route::post('/alt', 'MediaUploadController@alt_change_upload_media_file')->name('user.upload.media.file.alt.change');
            Route::post('/delete', 'MediaUploadController@delete_upload_media_file')->name('user.upload.media.file.delete');
        });
    });

    /*----------------------------------------------------------------
     | USER LOGIN / REGISTER
     |--------------------------------------------------------------*/
    Route::get('/login','Auth\LoginController@showLoginForm')->name('user.login')->middleware('math.captcha');
    Route::get('/login/wallet','Auth\WalletAuthController@showWalletLogin')->name('user.wallet.login');
    Route::post('/ajax-login','FrontendController@ajax_login')->name('user.ajax.login');
    Route::post('/login','Auth\LoginController@login')->middleware(['math.captcha','throttle:8,1']);
    Route::get('/login/forget-password','FrontendController@showUserForgetPasswordForm')->name('user.forget.password');
    Route::get('/login/reset-password/{user}/{token}','FrontendController@showUserResetPasswordForm')->name('user.reset.password');
    Route::post('/login/reset-password','FrontendController@UserResetPassword')->name('user.reset.password.change');
    Route::post('/login/forget-password','FrontendController@sendUserForgetPasswordMail');
    Route::post('/logout','Auth\LoginController@logout')->name('user.logout');
    Route::get('/user-logout','FrontendController@user_logout')->name('frontend.user.logout');
    Route::post('/register','Auth\RegisterController@register');
    Route::get('/register','Auth\RegisterController@showRegistrationForm')->name('user.register');
    Route::get('/user/email-verify','UserDashboardController@user_email_verify_index')->name('user.email.verify');
    Route::get('/user/resend-verify-code','UserDashboardController@reset_user_email_verify_code')->name('user.resend.verify.mail');
    Route::post('/user/email-verify','UserDashboardController@user_email_verify');
    Route::post('/package-user/generate-invoice','FrontendController@generate_package_invoice')->name('frontend.package.invoice.generate');
});

/*----------------------------------------------------------------
 | ADMIN LOGIN
 |--------------------------------------------------------------*/
Route::middleware(['setlang:backend'])->group(function (){
    Route::get('/login/admin','Auth\LoginController@showAdminLoginForm')->name('admin.login')->middleware('math.captcha');
    Route::get('/login/admin/forget-password','FrontendController@showAdminForgetPasswordForm')->name('admin.forget.password');
    Route::get('/login/admin/reset-password/{user}/{token}','FrontendController@showAdminResetPasswordForm')->name('admin.reset.password');
    Route::post('/login/admin/reset-password','FrontendController@AdminResetPassword')->name('admin.reset.password.change');
    Route::post('/login/admin/forget-password','FrontendController@sendAdminForgetPasswordMail');
    Route::get('/logout/admin','Admin\AdminDashboardController@adminLogout')->name('admin.logout');
    Route::post('/login/admin','Auth\LoginController@adminLogin')->middleware(['math.captcha','throttle:8,1']);
});

/*
|--------------------------------------------------------------
| Load extracted route files
|--------------------------------------------------------------
*/
Route::middleware(['setlang:frontend', 'globalVariable', 'maintains_mode'])->group(function () {
    require __DIR__.'/blockchain.php';
});
require __DIR__.'/admin.php';
