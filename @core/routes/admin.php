<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
| Extracted from web.php (~1100 lines) for maintainability.
| All routes are prefixed with 'admin-home' and protected by
| setlang:backend + adminglobalVariable middleware.
*/

Route::prefix('admin-home')->middleware(['setlang:backend','adminglobalVariable'])->group(function () {

    /*----------------------------------------------------------------
     | JOB POST ROUTES
     |--------------------------------------------------------------*/
    Route::group(['prefix' => 'jobs', 'namespace' => 'Admin'], function () {
        Route::get('/all', 'JobsController@all_jobs')->name('admin.jobs.all');
        Route::get('/new', 'JobsController@new_job')->name('admin.jobs.new');
        Route::post('/new', 'JobsController@store_job');
        Route::get('/edit/{id}', 'JobsController@edit_job')->name('admin.jobs.edit');
        Route::post('/update', 'JobsController@update_job')->name('admin.jobs.update');
        Route::post('/delete/{id}', 'JobsController@delete_job')->name('admin.jobs.delete');
        Route::post('/clone', 'JobsController@clone_job')->name('admin.jobs.clone');
        Route::post('/bulk-action', 'JobsController@bulk_action')->name('admin.jobs.bulk.action');

        Route::get('/applicant', 'JobsController@all_jobs_applicant')->name('admin.jobs.applicant');
        Route::post('/applicant/delete/{id}', 'JobsController@delete_job_applicant')->name('admin.jobs.applicant.delete');
        Route::post('/applicant/bulk-delete', 'JobsController@job_applicant_bulk_delete')->name('admin.jobs.applicant.bulk.delete');
        Route::get('/applicant/report', 'JobsController@job_applicant_report')->name('admin.jobs.applicant.report');
        Route::post('/applicant/mail', 'JobsController@job_applicant_mail')->name('admin.jobs.applicant.mail');
        Route::get('/applicant/view/{id}', 'JobsController@job_applicant_view')->name('admin.jobs.applicant.view');

        Route::get('/settings', 'JobsController@settings')->name('admin.jobs.settings');
        Route::post('/settings', 'JobsController@update_settings');
    });

    Route::group(['prefix' => 'jobs', 'namespace' => 'Admin'], function () {
        Route::get('/category', 'JobsCategoryController@all_jobs_category')->name('admin.jobs.category.all');
        Route::post('/category/new', 'JobsCategoryController@store_jobs_category')->name('admin.jobs.category.new');
        Route::post('/category/update', 'JobsCategoryController@update_jobs_category')->name('admin.jobs.category.update');
        Route::post('/category/delete/{id}', 'JobsCategoryController@delete_jobs_category')->name('admin.jobs.category.delete');
        Route::post('/category/bulk-action', 'JobsCategoryController@bulk_action')->name('admin.jobs.category.bulk.action');
        Route::post('/category/lang', 'JobsCategoryController@Language_by_slug')->name('admin.jobs.category.by.lang');
    });

    /*----------------------------------------------------------------
     | EVENTS ROUTES
     |--------------------------------------------------------------*/
    Route::group(['prefix' => 'events', 'namespace' => 'Admin'], function () {
        Route::get('', 'EventsController@all_events')->name('admin.events.all');
        Route::get('/new', 'EventsController@new_event')->name('admin.events.new');
        Route::post('/new', 'EventsController@store_event');
        Route::get('/edit/{id}', 'EventsController@edit_event')->name('admin.events.edit');
        Route::post('/update', 'EventsController@update_event')->name('admin.events.update');
        Route::post('/delete/{id}', 'EventsController@delete_event')->name('admin.events.delete');
        Route::post('/clone', 'EventsController@clone_event')->name('admin.events.clone');
        Route::post('/bulk-action', 'EventsController@bulk_action')->name('admin.events.bulk.action');

        Route::get('/page-settings', 'EventsController@page_settings')->name('admin.events.page.settings');
        Route::post('/page-settings', 'EventsController@update_page_settings');
        Route::get('/single-page-settings', 'EventsController@single_page_settings')->name('admin.events.single.page.settings');
        Route::post('/single-page-settings', 'EventsController@update_single_page_settings');

        Route::get('/event-attendance-logs', 'EventsController@event_attendance_logs')->name('admin.event.attendance.logs');
        Route::post('/event-attendance-logs', 'EventsController@update_event_attendance_logs_status');
        Route::post('/event-attendance-logs/delete/{id}', 'EventsController@delete_event_attendance_logs')->name('admin.event.attendance.logs.delete');
        Route::post('/event-attendance-logs/send-mail', 'EventsController@send_mail_event_attendance_logs')->name('admin.event.attendance.send.mail');
        Route::post('/event-attendance-logs/bulk-action', 'EventsController@attendance_logs_bulk_action')->name('admin.event.attendance.bulk.action');
        Route::post('/event-attendance/reminder', 'EventsController@event_attedance_reminder')->name('admin.event.attendance.reminder');
        Route::get('/event-attendance/view/{id}', 'EventsController@event_attendance_view')->name('admin.event.attendance.view');

        Route::get('/event-payment-logs', 'EventsController@event_payment_logs')->name('admin.event.payment.logs');
        Route::post('/event-payment-logs/delete/{id}', 'EventsController@delete_event_payment_logs')->name('admin.event.payment.delete');
        Route::post('/event-payment-logs/approve/{id}', 'EventsController@approve_event_payment')->name('admin.event.payment.approve');
        Route::post('/event-payment-logs/bulk-action', 'EventsController@payment_logs_bulk_action')->name('admin.event.payment.bulk.action');
        Route::get('/event-payment-logs/view/{id}', 'EventsController@payment_logs_view')->name('admin.event.payment.view');

        Route::get('/payment/report', 'EventsController@payment_report')->name('admin.event.payment.report');
        Route::get('/attendance/report', 'EventsController@attendance_report')->name('admin.event.attendance.report');
    });

    Route::group(['prefix' => 'events', 'namespace' => 'Admin'], function () {
        Route::get('/category', 'EventsCategoryController@all_events_category')->name('admin.events.category.all');
        Route::post('/category/new', 'EventsCategoryController@store_events_category')->name('admin.events.category.new');
        Route::post('/category/update', 'EventsCategoryController@update_events_category')->name('admin.events.category.update');
        Route::post('/category/delete/{id}', 'EventsCategoryController@delete_events_category')->name('admin.events.category.delete');
        Route::post('/category/lang', 'EventsCategoryController@Category_by_language_slug')->name('admin.events.category.by.lang');
        Route::post('/category/bulk-action', 'EventsCategoryController@bulk_action')->name('admin.events.category.bulk.action');
    });

    Route::group(['prefix' => 'events', 'namespace' => 'Admin'], function () {
        Route::get('/page-manage','EventsPageManageController@events_page_manage')->name('admin.event.page.manage');
        Route::post('/page-manage','EventsPageManageController@update_events_page_manage');
    });

    /*----------------------------------------------------------------
     | DONATIONS / CAUSES ROUTES
     |--------------------------------------------------------------*/
    Route::group(['prefix' => 'donations', 'namespace' => 'Admin'], function () {
        Route::get('/', 'CausesController@all_donation')->name('admin.donations.all');
        Route::get('/pending', 'CausesController@all_pending_donation')->name('admin.donations.pending.all');
        Route::get('/new', 'CausesController@new_donation')->name('admin.donations.new');
        Route::post('/new', 'CausesController@store_donation');
        Route::get('/edit/{id}', 'CausesController@edit_donation')->name('admin.donations.edit');
        Route::get('/donors/{id}', 'CausesController@donated_donors')->name('admin.donations.donors');
        Route::post('/update', 'CausesController@update_donation')->name('admin.donations.update');
        Route::post('/verify-wallet/{id}', 'CausesController@verify_wallet')->name('admin.causes.verify.wallet');
        Route::post('/delete/{id}', 'CausesController@delete_donation')->name('admin.donations.delete');
        Route::post('/clone', 'CausesController@clone_donation')->name('admin.donations.clone');
        Route::post('/bulk-action', 'CausesController@bulk_action')->name('admin.donations.bulk.action');
        Route::post('/reminder', 'CausesController@donation_reminder')->name('admin.donation.reminder');
        Route::post('/approve', 'CausesController@donation_approve')->name('admin.donation.approve');
        Route::post('/flag-fraud/{id}', 'CausesController@flag_fraud')->name('admin.donations.flag.fraud');
        Route::post('/change-status', 'CausesController@change_status')->name('admin.donation.change.status');

        Route::get('/settings', 'CausesController@settings')->name('admin.donations.settings');
        Route::post('/settings', 'CausesController@update_settings');
        Route::get('/single-page-variant', 'CausesController@single_variant')->name('admin.donations.single.page.variant');
        Route::post('/single-page-variant', 'CausesController@update_single_variant');
        Route::get('/report', 'CausesController@donation_report')->name('admin.donations.report');

        Route::get('/payment-logs', 'CausesController@donation_payment_logs')->name('admin.donations.payment.logs');
        Route::post('/payment-logs/delete/{id}', 'CausesController@delete_donation_payment_logs')->name('admin.donations.payment.delete');
        Route::post('/payment-logs/approve/{id}', 'CausesController@approve_donation_payment')->name('admin.donations.payment.approve');
        Route::post('/payment-logs/bulk-action', 'CausesController@donation_payment_logs_bulk_action')->name('admin.donations.payment.bulk.action');

        Route::group(['prefix' => 'category'], function () {
            Route::get('/', 'CausesCategoryController@all_donation_category')->name('admin.donations.category.all');
            Route::post('/new', 'CausesCategoryController@store_donation_category')->name('admin.donations.category.new');
            Route::post('/update', 'CausesCategoryController@update_donation_category')->name('admin.donations.category.update');
            Route::post('/delete/{id}', 'CausesCategoryController@delete_donation_category')->name('admin.donations.category.delete');
            Route::post('/lang', 'CausesCategoryController@Category_by_language_slug')->name('admin.donations.category.by.lang');
            Route::post('/bulk-action', 'CausesCategoryController@bulk_action')->name('admin.donations.category.bulk.action');
        });

        Route::group(['prefix' => 'gift'], function () {
            Route::get('/', 'CausesGiftController@all_donation_gift')->name('admin.donations.gift.all');
            Route::get('/create', 'CausesGiftController@create_donation_gift')->name('admin.donations.gift.create');
            Route::get('/edit/{id}', 'CausesGiftController@edit_donation_gift')->name('admin.donations.gift.edit');
            Route::post('/store', 'CausesGiftController@store_donation_gift')->name('admin.donations.gift.store');
            Route::post('/update', 'CausesGiftController@update_donation_gift')->name('admin.donations.gift.update');
            Route::post('/delete/{id}', 'CausesGiftController@delete_donation_gift')->name('admin.donations.gift.delete');
            Route::post('/bulk-action', 'CausesGiftController@bulk_action')->name('admin.donations.gift.bulk.action');
        });

        Route::group(['prefix' => 'cause-update'], function () {
            Route::get('/new/{id}', 'UpdateCauseController@all_update_causes')->name('add.new.update.cause.page');
            Route::post('/new/{id}', 'UpdateCauseController@store_update_causes');
            Route::post('/update', 'UpdateCauseController@update_update_causes')->name('admin.donations.update.cause.update');
            Route::post('/delete/{id}', 'UpdateCauseController@delete_update_cause')->name('admin.donations.update.cause.delete');
            Route::post('/bulk-action', 'UpdateCauseController@bulk_action')->name('admin.donations.update.cause.bulk.action');
        });

        Route::group(['prefix' => 'cause-comment'], function () {
            Route::get('/all/{id}', 'CauseCommentController@all_cause_comment')->name('all.cause.comment.page');
            Route::post('/delete/{id}', 'CauseCommentController@delete_cause_comment')->name('admin.donations.cause.comment.delete');
            Route::post('/bulk-action', 'CauseCommentController@bulk_action')->name('admin.donations.cause.comment.bulk.action');
        });

        Route::group(['prefix' => 'withdraw'], function () {
            Route::get('/request/all', 'WithdrawController@all_donation_withdraw')->name('admin.all.donation.withdraw.request');
            Route::get('/edit/{id}', 'WithdrawController@edit_donation_withdraw')->name('admin.donations.withdraw.edit');
            Route::post('/update', 'WithdrawController@update_donation_withdraw')->name('admin.donations.withdraw.update');
            Route::post('/delete/{id}', 'WithdrawController@delete_donation_withdraw')->name('admin.donations.withdraw.delete');
            Route::get('/view/{id}', 'WithdrawController@view_donation_withdraw')->name('admin.donations.withdraw.view');
            Route::post('/bulk-action', 'WithdrawController@bulk_action')->name('admin.donations.withdraw.bulk.action');
            Route::match(['get','post'],'/approval/{id}', 'WithdrawController@Withdraw_Approval')->name('admin.donations.withdraw.approval');
        });

        Route::group(['prefix' => 'escrow'], function () {
            Route::get('/', 'EscrowController@index')->name('admin.donations.escrow.index');
            Route::post('/disburse', 'EscrowController@disburse')->name('admin.donations.escrow.disburse');
            Route::post('/refund', 'EscrowController@refund')->name('admin.donations.escrow.refund');
        });

        Route::group(['prefix' => 'flag-reports'], function () {
            Route::get('/', 'FlagReportController@index')->name('admin.donations.flag.reports');
            Route::get('/view/{id}', 'FlagReportController@view')->name('admin.donations.flag.reports.view');
            Route::post('/delete-faq/{id}', 'FlagReportController@delete')->name('admin.donations.flag.reports.delete');
            Route::post('/bulk-action', 'FlagReportController@bulk_action')->name('admin.donations.flag.reports.bulk.action');
            Route::post('/mail-send', 'FlagReportController@mail_send')->name('admin.donations.flag.report.mail.send');
            Route::post('/cause-status-change', 'FlagReportController@update_cause_status')->name('admin.donations.flag.reports.cause.status.change');
        });
    });

    /*----------------------------------------------------------------
     | TAX CERTIFICATES
     |--------------------------------------------------------------*/
    Route::group(['prefix' => 'tax-certificates','namespace' => 'Admin'], function () {
        Route::get('/all-requests', 'UserTaxController@all_tax_requests')->name('admin.all.tax.requests');
        Route::get('/generate-certificate', 'UserTaxController@generate_certificate')->name('admin.tax.generate.certificate');
        Route::post('/send-generated-certificate', 'UserTaxController@send_generated_certificate')->name('admin.tax.generate.certificate.send');
        Route::get('/information-settings', 'UserTaxController@tax_settings_page')->name('admin.tax.information.settings');
        Route::post('/information-settings', 'UserTaxController@tax_settings_update')->name('admin.tax.information.settings');
        Route::get('/label-settings', 'UserTaxController@tax_label_settings_page')->name('admin.tax.information.label.settings');
        Route::post('/label-settings', 'UserTaxController@tax_label_settings_update');
        Route::post('/delete-requests/{id}', 'UserTaxController@delete')->name('admin.tax.request.delete');
        Route::post('/bulk-action', 'UserTaxController@bulk_action')->name('admin.tax.request.bulk.action');
    });

    /*----------------------------------------------------------------
     | SUPPORT TICKETS
     |--------------------------------------------------------------*/
    Route::prefix('support-tickets')->middleware(['auth:admin'])->group(function () {
        Route::get('/', 'Admin\SupportTicketController@all_tickets')->name('admin.support.ticket.all');
        Route::get('/new', 'Admin\SupportTicketController@new_ticket')->name('admin.support.ticket.new');
        Route::post('/new', 'Admin\SupportTicketController@store_ticket');
        Route::post('/delete/{id}', 'Admin\SupportTicketController@delete')->name('admin.support.ticket.delete');
        Route::get('/view/{id}', 'Admin\SupportTicketController@view')->name('admin.support.ticket.view');
        Route::post('/bulk-action', 'Admin\SupportTicketController@bulk_action')->name('admin.support.ticket.bulk.action');
        Route::post('/priority-change', 'Admin\SupportTicketController@priority_change')->name('admin.support.ticket.priority.change');
        Route::post('/status-change', 'Admin\SupportTicketController@status_change')->name('admin.support.ticket.status.change');
        Route::post('/send message', 'Admin\SupportTicketController@send_message')->name('admin.support.ticket.send.message');
        Route::get('/page-settings', 'Admin\SupportTicketController@page_settings')->name('admin.support.ticket.page.settings');
        Route::post('/page-settings', 'Admin\SupportTicketController@update_page_settings');
        Route::group(['prefix' => 'department'], function () {
            Route::get('/', 'Admin\SupportTicketDepartmentController@category')->name('admin.support.ticket.department');
            Route::post('/', 'Admin\SupportTicketDepartmentController@new_category');
            Route::post('/delete/{id}', 'Admin\SupportTicketDepartmentController@delete')->name('admin.support.ticket.department.delete');
            Route::post('/update', 'Admin\SupportTicketDepartmentController@update')->name('admin.support.ticket.department.update');
            Route::post('/bulk-action', 'Admin\SupportTicketDepartmentController@bulk_action')->name('admin.support.ticket.department.bulk.action');
        });
    });

    /*----------------------------------------------------------------
     | HOME PAGE MANAGE (01,02,03)
     |--------------------------------------------------------------*/
    Route::group(['prefix'=>'home-page-01','namespace'=>'Admin\HomePages'], function() {
        Route::get('/latest-news','HomePageController@home_01_latest_news')->name('admin.homeone.latest.news');
        Route::post('/latest-news','HomePageController@home_01_update_latest_news');
        Route::get('/latest-event','HomePageController@home_01_latest_event')->name('admin.homeone.latest.event');
        Route::post('/latest-event','HomePageController@home_01_update_latest_event');
        Route::get('/testimonial','HomePageController@home_01_testimonial')->name('admin.homeone.testimonial');
        Route::post('/testimonial','HomePageController@home_01_update_testimonial');
        Route::get('/feature-area','HomePageController@home_01_feature_area')->name('admin.homeone.feature.area');
        Route::post('/feature-area','HomePageController@home_01_update_feature_area');
        Route::get('/about-us','HomePageController@home_01_about_us')->name('admin.homeone.about.us');
        Route::post('/about-us','HomePageController@home_01_update_about_us');
        Route::get('/video-area','HomePageController@home_01_video_area')->name('admin.homeone.video.area');
        Route::post('/video-area','HomePageController@home_01_update_video_area');
        Route::get('/section-manage-home-one-two-three','HomePageController@home_01_02_03_section_manage')->name('admin.homeone.section.manage');
        Route::post('/section-manage-home-one-two-three','HomePageController@update_home_01_02_03_section_manage');
        Route::get('/section-manage-home-four-five-six','HomePageController@home_04_05_06_section_manage')->name('admin.home.four.five.six.section.manage');
        Route::post('/section-manage-home-four-five-six','HomePageController@update_home_04_05_06_section_manage');
        Route::get('/team-member','HomePageController@home_01_team_member')->name('admin.homeone.team.member');
        Route::post('/team-member','HomePageController@home_01_update_team_member');
        Route::get('/donation-category-area','HomePageController@home_01_donation_category_area')->name('admin.homeone.donation.category.area');
        Route::post('/donation-category-area','HomePageController@home_01_update_donation_category_area');
        Route::get('/feature-cause-area','HomePageController@home_01_featured_cause_area')->name('admin.homeone.featured.cause.area');
        Route::post('/feature-cause-area','HomePageController@home_01_update_featured_cause_area');
        Route::get('/latest-cause-area','HomePageController@home_01_latest_cause_area')->name('admin.homeone.latest.cause.area');
        Route::post('/latest-cause-area','HomePageController@home_01_update_latest_cause_area');
        Route::get('/coutnerup-area','HomePageController@home_02_counterup_area')->name('admin.homeone.counterup.area');
        Route::post('/coutnerup-area','HomePageController@home_02_update_counterup_area');
        Route::get('/what-we-do-area','HomePageController@home_2_what_we_do_area')->name('admin.homeone.what.we.do.area');
        Route::post('/what-we-do-area','HomePageController@home_2_what_we_do_area_update');
        Route::get('/header','HeaderSliderController@index')->name('admin.header');
        Route::post('/header','HeaderSliderController@store');
        Route::post('/update-header','HeaderSliderController@update')->name('admin.header.update');
        Route::post('/delete-header/{id}','HeaderSliderController@delete')->name('admin.header.delete');
        Route::post('/header/bulk-action/','HeaderSliderController@bulk_action')->name('admin.header.bulk.action');
    });

    Route::group(['prefix'=>'home-page-04','namespace'=>'Admin\HomePages'], function() {
        Route::get('/header-area','HomePageFourController@header_area')->name('admin.home.four.header.area');
        Route::post('/header-area','HomePageFourController@update_header_area');
        Route::get('/feature-area','HomePageFourController@feature_area')->name('admin.home.four.feature.area');
        Route::post('/feature-area','HomePageFourController@update_feature_area');
        Route::get('/success-story-area','HomePageFourController@success_story_area')->name('admin.home.four.success.story.area');
        Route::post('/success-story-area','HomePageFourController@update_success_story_area');
        Route::get('/about-us-area','HomePageFourController@about_us_area')->name('admin.home.four.about.us.area');
        Route::post('/about-us-area','HomePageFourController@update_about_us_area');
        Route::get('/events-area','HomePageFourController@events_area')->name('admin.home.four.events.area');
        Route::post('/events-area','HomePageFourController@update_events_area');
        Route::get('/recent-causes-area','HomePageFourController@recent_causes_area')->name('admin.home.four.recent.causes.area');
        Route::post('/recent-causes-area','HomePageFourController@update_recent_causes_area');
        Route::get('/recent-blog-area','HomePageFourController@recent_blog_area')->name('admin.home.four.recent.blog.area');
        Route::post('/recent-blog-area','HomePageFourController@update_recent_blog_area');
    });

    Route::group(['prefix'=>'home-page-05','namespace'=>'Admin\HomePages'], function() {
        Route::get('/rise-area','HomePageFiveController@rise_area')->name('admin.home.five.rise.area');
        Route::post('/rise-area','HomePageFiveController@update_rise_area');
        Route::get('/feature-area','HomePageFiveController@feature_area')->name('admin.home.five.feature.area');
        Route::post('/feature-area','HomePageFiveController@update_feature_area');
        Route::get('/category-area','HomePageFiveController@category_area')->name('admin.home.five.category.area');
        Route::post('/category-area','HomePageFiveController@update_category_area');
        Route::get('/success-story-area','HomePageFiveController@success_story_area')->name('admin.home.five.success.story.area');
        Route::post('/success-story-area','HomePageFiveController@update_success_story_area');
        Route::get('/recent-causes-area','HomePageFiveController@recent_causes_area')->name('admin.home.five.recent.causes.area');
        Route::post('/recent-causes-area','HomePageFiveController@update_recent_causes_area');
        Route::get('/events-area','HomePageFiveController@events_area')->name('admin.home.five.events.area');
        Route::post('/events-area','HomePageFiveController@update_events_area');
        Route::get('/recent-blog-area','HomePageFiveController@recent_blog_area')->name('admin.home.five.recent.blog.area');
        Route::post('/recent-blog-area','HomePageFiveController@update_recent_blog_area');
    });

    Route::group(['prefix'=>'home-page-06','namespace'=>'Admin\HomePages'], function() {
        Route::get('/header-area','HomePageSixController@header_area')->name('admin.home.six.header.area');
        Route::post('/header-area','HomePageSixController@update_header_area');
        Route::get('/rise-area','HomePageSixController@rise_area')->name('admin.home.six.rise.area');
        Route::post('/rise-area','HomePageSixController@update_rise_area');
        Route::get('/feature-area','HomePageSixController@feature_area')->name('admin.home.six.feature.area');
        Route::post('/feature-area','HomePageSixController@update_feature_area');
        Route::get('/category-area','HomePageSixController@category_area')->name('admin.home.six.category.area');
        Route::post('/category-area','HomePageSixController@update_category_area');
        Route::get('/recent-causes-area','HomePageSixController@recent_causes_area')->name('admin.home.six.recent.causes.area');
        Route::post('/recent-causes-area','HomePageSixController@update_recent_causes_area');
        Route::get('/success-story-area','HomePageSixController@success_story_area')->name('admin.home.six.success.story.area');
        Route::post('/success-story-area','HomePageSixController@update_success_story_area');
        Route::get('/about-us-area','HomePageSixController@about_us_area')->name('admin.home.six.about.us.area');
        Route::post('/about-us-area','HomePageSixController@update_about_us_area');
        Route::get('/events-area','HomePageSixController@events_area')->name('admin.home.six.events.area');
        Route::post('/events-area','HomePageSixController@update_events_area');
    });

    Route::group(['prefix'=>'widgets','namespace'=>'Admin'], function() {
        Route::get('/all', 'WidgetsController@index')->name('admin.widgets');
        Route::post('/all', 'WidgetsController@new_widget')->name('admin.widgets.new');
        Route::post('/markup', 'WidgetsController@widget_markup')->name('admin.widgets.markup');
        Route::post('/update', 'WidgetsController@update_widget')->name('admin.widgets.update');
        Route::post('//update/order', 'WidgetsController@update_order_widget')->name('admin.widgets.update.order');
        Route::post('/delete', 'WidgetsController@delete_widget')->name('admin.widgets.delete');
    });

    Route::group(['prefix'=>'topbar-settings','namespace'=>'Admin'], function(){
        Route::get('/all', "TopbarController@index")->name('admin.topbar.settings');
        Route::post('/all', 'TopbarController@store');
        Route::post('/update', 'TopbarController@update')->name('admin.topbar.update');
        Route::post('/delete/{id}', 'TopbarController@delete')->name('admin.topbar.delete');
        Route::post('/bulk-action', 'TopbarController@bulk_action')->name('admin.topbar.bulk.action');
    });

    Route::group(['prefix'=>'menu','namespace'=>'Admin'], function() {
        Route::get('/', 'MenuController@index')->name('admin.menu');
        Route::post('/new-menu', 'MenuController@store_new_menu')->name('admin.menu.new');
        Route::get('/edit/{id}', 'MenuController@edit_menu')->name('admin.menu.edit');
        Route::post('/update/{id}', 'MenuController@update_menu')->name('admin.menu.update');
        Route::post('/delete/{id}', 'MenuController@delete_menu')->name('admin.menu.delete');
        Route::post('/default/{id}', 'MenuController@set_default_menu')->name('admin.menu.default');
        Route::post('/mega-menu', 'MenuController@mega_menu_item_select_markup')->name('admin.mega.menu.item.select.markup');
    });

    Route::get('company-manage', 'Admin\AdminDashboardController@company_manage')->name('admin.company.settings');
    Route::post('company-manage', 'Admin\AdminDashboardController@update_company_manage');

    Route::get('404-page-manage', 'Admin\Error404PageManage@error_404_page_settings')->name('admin.404.page.settings');
    Route::post('404-page-manage', 'Admin\Error404PageManage@update_error_404_page_settings');

    Route::get('/maintains-page/settings', 'Admin\MaintainsPageController@maintains_page_settings')->name('admin.maintains.page.settings');
    Route::post('/maintains-page/settings', 'Admin\MaintainsPageController@update_maintains_page_settings');

    Route::get('/register-page/settings', 'Admin\RegisterPageManageController@register_page_setting')->name('admin.register.page.settings');
    Route::post('/register-page/settings', 'Admin\RegisterPageManageController@update_register_page_setting');

    Route::group(['prefix'=>'form-builder','namespace'=>'Admin'], function() {
        Route::get('/get-in-touch', 'FormBuilderController@get_in_touch_form_index')->name('admin.form.builder.get.in.touch');
        Route::post('/get-in-touch', 'FormBuilderController@update_get_in_touch_form');
        Route::get('/donation-form', 'FormBuilderController@donation_form_index')->name('admin.form.builder.donation.form');
        Route::post('/donation-form', 'FormBuilderController@update_donation_form');
        Route::get('/service-query', 'FormBuilderController@service_query_index')->name('admin.form.builder.service.query');
        Route::post('/service-query', 'FormBuilderController@update_service_query');
        Route::get('/case-study-query', 'FormBuilderController@case_study_query_index')->name('admin.form.builder.case.study.query');
        Route::post('/case-study-query', 'FormBuilderController@update_case_study_query');
        Route::get('/quote-form', 'FormBuilderController@quote_form_index')->name('admin.form.builder.quote');
        Route::post('/quote-form', 'FormBuilderController@update_quote_form');
        Route::get('/order-form', 'FormBuilderController@order_form_index')->name('admin.form.builder.order');
        Route::post('/order-form', 'FormBuilderController@update_order_form');
        Route::get('/contact-form', 'FormBuilderController@contact_form_index')->name('admin.form.builder.contact');
        Route::post('/contact-form', 'FormBuilderController@update_contact_form');
        Route::get('/apply-job-form', 'FormBuilderController@apply_job_form_index')->name('admin.form.builder.apply.job.form');
        Route::post('/apply-job-form', 'FormBuilderController@update_apply_job_form');
        Route::get('/event-attendance', 'FormBuilderController@event_attendance_form_index')->name('admin.form.builder.event.attendance.form');
        Route::post('/event-attendance', 'FormBuilderController@update_event_attedance_form');
    });

    Route::prefix('home-page')->namespace('Admin\HomePages')->group(function () {
        Route::get('/header', 'HeaderSliderController@index')->name('admin.home.header');
        Route::post('/header', 'HeaderSliderController@store');
        Route::post('/update-header', 'HeaderSliderController@update')->name('admin.home.header.update');
        Route::post('/delete-header/{id}', 'HeaderSliderController@delete')->name('admin.home.header.delete');
        Route::post('/header/bulk-action/', 'HeaderSliderController@bulk_action')->name('admin.home.header.bulk.action');
        Route::get('/key-features-area', 'HomePageController@key_features_section')->name('admin.home.key.features');
        Route::post('/key-features-area', 'HomePageController@update_key_features_section');
        Route::get('/counterup-area', 'HomePageController@counterup_area')->name('admin.home.counter.up.area');
        Route::post('/counterup-area', 'HomePageController@update_counterup_area');
        Route::get('/why-choose-us-area-settings', 'HomePageController@why_choose_us_area')->name('admin.home.why.choose.us');
        Route::post('/why-choose-us-area-settings', 'HomePageController@update_why_choose_us_area');
        Route::get('/call-to-action-settings', 'HomePageController@call_to_action_area')->name('admin.home.call.to.action');
        Route::post('/call-to-action-settings', 'HomePageController@update_call_to_action_area');
        Route::get('/testimonial-area-settings', 'HomePageController@testimonial_area')->name('admin.home.testimonial');
        Route::post('/testimonial-area-settings', 'HomePageController@update_testimonial_area');
        Route::get('/keyfeatures-area-settings', 'HomePageController@keyfeatures_area')->name('admin.home.keyfeatures');
        Route::post('/keyfeatures-area-settings', 'HomePageController@update_keyfeatures_area');
        Route::get('/price-plan-area-settings', 'HomePageController@price_plan_area')->name('admin.home.price.plan');
        Route::post('/price-plan-area-settings', 'HomePageController@update_price_plan_area');
        Route::get('/latest-blog-settings', 'HomePageController@latest_blog_area')->name('admin.home.blog.latest');
        Route::post('/latest-blog-settings', 'HomePageController@update_latest_blog_area');
        Route::get('/counterup-settings', 'HomePageController@counterup_settings')->name('admin.home.counterup');
        Route::post('/counterup-settings', 'HomePageController@update_counterup_settings');
        Route::get('/section-manage', 'HomePageController@section_manage')->name('admin.home.section.manage');
        Route::post('/section-manage', 'HomePageController@update_section_manage');
    });

    Route::group(['prefix'=>'about-page','namespace'=>'Admin'], function () {
        Route::get('/about-us','AboutUsPageController@about_page_about_section')->name('admin.about.page.about');
        Route::post('/about-us','AboutUsPageController@about_page_update_about_section');
        Route::get('/our-mission','AboutUsPageController@about_page_our_section')->name('admin.about.our.mission');
        Route::post('/our-mission','AboutUsPageController@about_page_update_our_section');
        Route::get('/counterup','AboutUsPageController@about_page_counterup_section')->name('admin.about.counterup');
        Route::post('/counterup','AboutUsPageController@about_page_update_counterup_section');
        Route::get('/what-we-do','AboutUsPageController@about_page_what_we_do_section')->name('admin.about.what.we.do');
        Route::post('/what-we-do','AboutUsPageController@about_page_update_what_we_do_section');
        Route::get('/team-member','AboutUsPageController@about_page_team_member_section')->name('admin.about.team.member');
        Route::post('/team-member','AboutUsPageController@about_page_update_team_member_section');
        Route::get('/testimonial','AboutUsPageController@about_page_testimonial_section')->name('admin.about.testimonial');
        Route::post('/testimonial','AboutUsPageController@about_page_update_testimonial_section');
        Route::get('/section-manage','AboutUsPageController@about_page_section_manage')->name('admin.about.page.section.manage');
        Route::post('/section-manage','AboutUsPageController@about_page_update_section_manage');
    });

    Route::group(['prefix'=>'contact-page','namespace'=>'Admin'], function (){
        Route::get('/form-area','ContactPageController@contact_page_form_area')->name('admin.contact.page.form.area');
        Route::post('/form-area','ContactPageController@contact_page_update_form_area');
        Route::get('/map','ContactPageController@contact_page_map_area')->name('admin.contact.page.map');
        Route::post('/map','ContactPageController@contact_page_update_map_area');
        Route::get('/section-manage','ContactPageController@contact_page_section_manage')->name('admin.contact.page.section.manage');
        Route::post('/section-manage','ContactPageController@contact_page_update_section_manage');
        Route::get('/contact-info','ContactInfoController@index')->name('admin.contact.info');
        Route::post('/contact-info','ContactInfoController@store');
        Route::post('/contact-info/title','ContactInfoController@contact_info_title')->name('admin.contact.info.title');
        Route::post('/contact-info/update','ContactInfoController@update')->name('admin.contact.info.update');
        Route::post('/contact-info/delete/{id}','ContactInfoController@delete')->name('admin.contact.info.delete');
        Route::post('/contact-info/bulk-action','ContactInfoController@bulk_action')->name('admin.contact.info.bulk.action');
    });

    Route::group(['prefix'=>'success-story-page','namespace'=>'Admin'], function (){
        Route::get('/success-story-area','SuccessStoryController@sucess_story')->name('admin.success.story.page.manage');
        Route::post('/success-story-area','SuccessStoryController@update_sucess_story');
    });

    Route::group(['prefix'=>'media-upload','namespace'=>'Admin'], function () {
        Route::post('/alt', 'MediaUploadController@alt_change_upload_media_file')->name('admin.upload.media.file.alt.change');
        Route::get('/page', 'MediaUploadController@all_upload_media_images_for_page')->name('admin.upload.media.images.page');
        Route::post('/delete', 'MediaUploadController@delete_upload_media_file')->name('admin.upload.media.file.delete');
    });

    Route::group(['namespace'=>'Admin'], function () {
        Route::get('/settings', 'AdminDashboardController@admin_settings')->name('admin.profile.settings');
        Route::get('/profile-update', 'AdminDashboardController@admin_profile')->name('admin.profile.update');
        Route::post('/profile-update', 'AdminDashboardController@admin_profile_update');
        Route::get('/password-change', 'AdminDashboardController@admin_password')->name('admin.password.change');
        Route::post('/password-change', 'AdminDashboardController@admin_password_chagne');
        Route::get('/', 'AdminDashboardController@adminIndex')->name('admin.home');
        Route::get('/dark-mode-toggle', 'AdminDashboardController@dark_mode_toggle')->name('admin.dark.mode.toggle');
        Route::get('/navbar-settings', "AdminDashboardController@navbar_settings")->name('admin.navbar.settings');
        Route::post('/navbar-settings', "AdminDashboardController@update_navbar_settings");
    });

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::get('/new-user','AdminRoleManageController@new_user')->name('admin.new.user');
        Route::post('/new-user','AdminRoleManageController@new_user_add');
        Route::get('/user-edit/{id}','AdminRoleManageController@user_edit')->name('admin.user.edit');
        Route::post('/user-update','AdminRoleManageController@user_update')->name('admin.user.update');
        Route::post('/user-password-change','AdminRoleManageController@user_password_change')->name('admin.user.password.change');
        Route::post('/delete-user/{id}','AdminRoleManageController@new_user_delete')->name('admin.delete.user');
        Route::get('/all','AdminRoleManageController@all_user')->name('admin.all.user');
        Route::get('/role','AdminRoleManageController@all_admin_role')->name('admin.all.admin.role');
        Route::get('/role/new','AdminRoleManageController@new_admin_role_index')->name('admin.role.new');
        Route::post('/role/new','AdminRoleManageController@store_new_admin_role');
        Route::get('/role/edit/{id}','AdminRoleManageController@edit_admin_role')->name('admin.user.role.edit');
        Route::post('/role/update','AdminRoleManageController@update_admin_role')->name('admin.user.role.update');
        Route::post('/role/delete/{id}','AdminRoleManageController@delete_admin_role')->name('admin.user.role.delete');
    });

    Route::group(['prefix' => 'frontend', 'namespace' => 'Admin'], function () {
        Route::get('/new-user', 'FrontendUserManageController@new_user')->name('admin.frontend.new.user');
        Route::post('/new-user', 'FrontendUserManageController@new_user_add');
        Route::post('/user-update', 'FrontendUserManageController@user_update')->name('admin.frontend.user.update');
        Route::post('/user-password-chnage', 'FrontendUserManageController@user_password_change')->name('admin.frontend.user.password.change');
        Route::post('/delete-user/{id}', 'FrontendUserManageController@new_user_delete')->name('admin.frontend.delete.user');
        Route::get('/all-user', 'FrontendUserManageController@all_user')->name('admin.all.frontend.user');
        Route::get('/user/tax/{id}', 'FrontendUserManageController@user_tax_view')->name('admin.frontend.user.tax.information');
        Route::get('/user/verify-information/{id}', 'FrontendUserManageController@user_verify_view')->name('admin.frontend.user.verify.information');
        Route::get('/user/verify-update/{id}', 'FrontendUserManageController@user_verify_update')->name('admin.frontend.user.verify.update');
        Route::post('/all-user/bulk-action', 'FrontendUserManageController@bulk_action')->name('admin.all.frontend.user.bulk.action');
        Route::post('/all-user/email-status', 'FrontendUserManageController@email_status')->name('admin.all.frontend.user.email.status');
        Route::post('/all-user/campaign-permission', 'FrontendUserManageController@campaign_permission')->name('admin.frontend.user.campaign.permission');
    });

    Route::group(['prefix' => 'newsletter', 'namespace' => 'Admin'], function () {
        Route::get('/', 'NewsletterController@index')->name('admin.newsletter');
        Route::post('/delete/{id}', 'NewsletterController@delete')->name('admin.newsletter.delete');
        Route::post('/single', 'NewsletterController@send_mail')->name('admin.newsletter.single.mail');
        Route::get('/all', 'NewsletterController@send_mail_all_index')->name('admin.newsletter.mail');
        Route::post('/all', 'NewsletterController@send_mail_all');
        Route::post('/new', 'NewsletterController@add_new_sub')->name('admin.newsletter.new.add');
        Route::post('/bulk-action', 'NewsletterController@bulk_action')->name('admin.newsletter.bulk.action');
        Route::post('/newsletter/verify-mail-send','NewsletterController@verify_mail_send')->name('admin.newsletter.verify.mail.send');
        Route::get('/newsletter/unsubscribe/{id}','NewsletterController@newsletter_unsubscribe')->name('user.newsletter.unsubscribe');
    });

    Route::group(['prefix' => 'blog', 'namespace' => 'Admin'], function () {
        Route::get('/', 'BlogController@index')->name('admin.blog');
        Route::get('/new', 'BlogController@new_blog')->name('admin.blog.new');
        Route::post('/new', 'BlogController@store_new_blog');
        Route::post('/clone', 'BlogController@clone_blog')->name('admin.blog.clone');
        Route::get('/edit/{id}', 'BlogController@edit_blog')->name('admin.blog.edit');
        Route::post('/update/{id}', 'BlogController@update_blog')->name('admin.blog.update');
        Route::post('/delete/{id}', 'BlogController@delete_blog')->name('admin.blog.delete');
        Route::get('/category', 'BlogController@category')->name('admin.blog.category');
        Route::post('/category', 'BlogController@new_category');
        Route::post('/category/delete/{id}', 'BlogController@delete_category')->name('admin.blog.category.delete');
        Route::post('/category/update', 'BlogController@update_category')->name('admin.blog.category.update');
        Route::post('/category/bulk-action', 'BlogController@category_bulk_action')->name('admin.blog.category.bulk.action');
        Route::post('/blog-lang-by-cat', 'BlogController@Language_by_slug')->name('admin.blog.lang.cat');
        Route::get('/page-settings', 'BlogController@blog_page_settings')->name('admin.blog.page.settings');
        Route::post('/page-settings', 'BlogController@update_blog_page_settings');
        Route::get('/single-settings', 'BlogController@blog_single_page_settings')->name('admin.blog.single.settings');
        Route::post('/single-settings', 'BlogController@update_blog_single_page_settings');
        Route::post('/bulk-action', 'BlogController@bulk_action')->name('admin.blog.bulk.action');
    });

    Route::group(['prefix' => 'success-story', 'namespace' => 'Admin'], function () {
        Route::get('/', 'SuccessStoryController@index')->name('admin.success.story');
        Route::get('/new', 'SuccessStoryController@new_success_story')->name('admin.success.story.new');
        Route::post('/new', 'SuccessStoryController@store_new_success_story');
        Route::post('/clone', 'SuccessStoryController@clone_success_story')->name('admin.success.story.clone');
        Route::get('/edit/{id}', 'SuccessStoryController@edit_success_story')->name('admin.success.story.edit');
        Route::post('/update/{id}', 'SuccessStoryController@update_success_story')->name('admin.success.story.update');
        Route::post('/delete/{id}', 'SuccessStoryController@delete_success_story')->name('admin.success.story.delete');
        Route::post('/bulk-action', 'SuccessStoryController@bulk_action')->name('admin.success.story.bulk.action');
        Route::group(['prefix' => 'category'], function () {
            Route::get('/', 'SuccessStoryCategoryController@category')->name('admin.success.story.category');
            Route::post('/', 'SuccessStoryCategoryController@new_category');
            Route::post('/update', 'SuccessStoryCategoryController@update_category')->name('admin.success.story.category.update');
            Route::post('/delete/{id}', 'SuccessStoryCategoryController@delete_category')->name('admin.success.story.category.delete');
            Route::post('/bulk-action', 'SuccessStoryCategoryController@category_bulk_action')->name('admin.success.story.category.bulk.action');
        });
    });

    Route::group(['prefix' => 'client-area', 'namespace' => 'Admin'], function () {
        Route::get('/', 'ClientAreaController@index')->name('admin.client.area');
        Route::post('/', 'ClientAreaController@store');
        Route::post('/update', 'ClientAreaController@update')->name('admin.client.area.update');
        Route::post('/delete/{id}', 'ClientAreaController@delete')->name('admin.client.area.delete');
        Route::post('/bulk-action', 'ClientAreaController@bulk_action')->name('admin.client.area.bulk.action');
    });

    Route::group(['prefix' => 'gallery-page', 'namespace' => 'Admin'], function () {
        Route::get('/', 'ImageGalleryPageController@index')->name('admin.gallery.all');
        Route::post('/new', 'ImageGalleryPageController@store')->name('admin.gallery.new');
        Route::post('/update', 'ImageGalleryPageController@update')->name('admin.gallery.update');
        Route::post('/delete/{id}', 'ImageGalleryPageController@delete')->name('admin.gallery.delete');
        Route::post('/bulk-action', 'ImageGalleryPageController@bulk_action')->name('admin.gallery.bulk.action');
        Route::get('/page-settings', 'ImageGalleryPageController@page_settings')->name('admin.gallery.page.settings');
        Route::post('/page-settings', 'ImageGalleryPageController@update_page_settings');
        Route::get('/category', 'ImageGalleryPageController@category_index')->name('admin.gallery.category');
        Route::post('/category/new', 'ImageGalleryPageController@category_store')->name('admin.gallery.category.new');
        Route::post('/category/update', 'ImageGalleryPageController@category_update')->name('admin.gallery.category.update');
        Route::post('/category/delete/{id}', 'ImageGalleryPageController@category_delete')->name('admin.gallery.category.delete');
        Route::post('/category/bulk-action', 'ImageGalleryPageController@category_bulk_action')->name('admin.gallery.category.bulk.action');
        Route::post('/category-by-slug', 'ImageGalleryPageController@category_by_slug')->name('admin.gallery.category.by.lang');
    });

    Route::group(['prefix' => 'faq', 'namespace' => 'Admin'], function () {
        Route::get('/', 'FaqController@index')->name('admin.faq');
        Route::post('/', 'FaqController@store');
        Route::post('/update-faq', 'FaqController@update')->name('admin.faq.update');
        Route::post('/delete-faq/{id}', 'FaqController@delete')->name('admin.faq.delete');
        Route::post('/clone-faq', 'FaqController@clone')->name('admin.faq.clone');
        Route::post('/faq/bulk-action', 'FaqController@bulk_action')->name('admin.faq.bulk.action');
    });

    Route::group(['prefix' => 'team-member', 'namespace' => 'Admin'], function () {
        Route::get('/all', 'TeamMemberController@index')->name('admin.team.member');
        Route::post('/all', 'TeamMemberController@store');
        Route::post('/update', 'TeamMemberController@update')->name('admin.team.member.update');
        Route::post('/delete/{id}', 'TeamMemberController@delete')->name('admin.team.member.delete');
        Route::post('/clone', 'TeamMemberController@clone')->name('admin.team.member.clone');
        Route::post('/bulk-action', 'TeamMemberController@bulk_action')->name('admin.team.member.bulk.action');
    });

    Route::group(['prefix' => 'page', 'namespace' => 'Admin'], function () {
        Route::get('/all', 'PagesController@index')->name('admin.page');
        Route::get('/new', 'PagesController@new_page')->name('admin.page.new');
        Route::post('/new', 'PagesController@store_new_page');
        Route::get('/edit/{id}', 'PagesController@edit_page')->name('admin.page.edit');
        Route::post('/update/{id}', 'PagesController@update_page')->name('admin.page.update');
        Route::post('/delete/{id}', 'PagesController@delete_page')->name('admin.page.delete');
        Route::post('/bulk-action', 'PagesController@bulk_action')->name('admin.page.bulk.action');
    });

    Route::group(['prefix' => 'testimonial', 'namespace' => 'Admin'], function () {
        Route::get('/all','TestimonialController@index')->name('admin.testimonial');
        Route::post('/all','TestimonialController@store');
        Route::post('/clone','TestimonialController@clone')->name('admin.testimonial.clone');
        Route::post('/update','TestimonialController@update')->name('admin.testimonial.update');
        Route::post('/delete/{id}','TestimonialController@delete')->name('admin.testimonial.delete');
        Route::post('/bulk-action','TestimonialController@bulk_action')->name('admin.testimonial.bulk.action');
    });

    Route::group(['prefix' => 'mobile-slider', 'namespace' => 'Admin'], function () {
        Route::get('/all','MobileSliderController@index')->name('admin.mobile.slider');
        Route::post('/all','MobileSliderController@store');
        Route::post('/clone','MobileSliderController@clone')->name('admin.mobile.slider.clone');
        Route::post('/update','MobileSliderController@update')->name('admin.mobile.slider.update');
        Route::post('/delete/{id}','MobileSliderController@delete')->name('admin.mobile.slider.delete');
        Route::post('/bulk-action','MobileSliderController@bulk_action')->name('admin.mobile.slider.bulk.action');
    });

    Route::group(['prefix'=>'reward','namespace'=>'Admin'], function (){
        Route::get('/all','RewardController@index')->name('admin.reward');
        Route::post('/all','RewardController@store');
        Route::post('/update','RewardController@update')->name('admin.reward.update');
        Route::post('/delete/{id}','RewardController@delete')->name('admin.reward.delete');
        Route::post('/bulk-action','RewardController@bulk_action')->name('admin.reward.bulk.action');
        Route::get('/settings','RewardController@settings')->name('admin.reward.settings');
        Route::post('/settings','RewardController@update_settings');
        Route::group(['prefix' => 'redeem'], function () {
            Route::get('/request/all', 'RewardRedeemController@all_reward_redeem')->name('admin.reward.all.redeem.request');
            Route::get('/edit/{id}', 'RewardRedeemController@edit_reward_redeem')->name('admin.reward.redeem.edit');
            Route::post('/update', 'RewardRedeemController@update_reward_redeem')->name('admin.reward.redeem.update');
            Route::post('/delete/{id}', 'RewardRedeemController@delete_reward_redeem')->name('admin.reward.redeem.delete');
            Route::get('/view/{id}', 'RewardRedeemController@view_reward_redeem')->name('admin.reward.redeem.view');
            Route::post('/bulk-action', 'RewardRedeemController@bulk_action')->name('admin.reward.redeem.bulk.action');
            Route::get('/approval/{id}', 'RewardRedeemController@redeem_Approval')->name('admin.reward.redeem.approval');
        });
    });

    Route::group(['prefix'=>'counterup','namespace'=>'Admin'], function (){
        Route::get('/all','CounterUpController@index')->name('admin.counterup');
        Route::post('/all','CounterUpController@store');
        Route::post('/update','CounterUpController@update')->name('admin.counterup.update');
        Route::post('/delete/{id}','CounterUpController@delete')->name('admin.counterup.delete');
        Route::post('/bulk-action','CounterUpController@bulk_action')->name('admin.counterup.bulk.action');
    });

    Route::group(['prefix'=>'notification','namespace'=>'Admin'], function (){
        Route::get('/all','NotificationController@index')->name('admin.notification');
        Route::get('/view/{id}','NotificationController@view')->name('admin.notification.view');
        Route::post('/delete/{id}','NotificationController@delete')->name('admin.notification.delete');
        Route::post('/bulk-action','NotificationController@bulk_action')->name('admin.notification.bulk.action');
    });

    Route::group(['prefix'=>'appearance-settings/navbar','namespace'=>'Admin'], function () {
        Route::get('/all', 'NavbarController@navbar_settings')->name('admin.navbar.settings');
        Route::post('/all', 'NavbarController@update_navbar_settings');
    });

    Route::group(['prefix'=>'appearance-settings/home-variant','namespace'=>'Admin'], function () {
        Route::get('/select', "AdminDashboardController@home_variant")->name('admin.home.variant');
        Route::post('/select', "AdminDashboardController@update_home_variant");
    });

    Route::group(['prefix'=>'appearance-settings/topbar','namespace'=>'Admin'], function () {
        Route::get('/all',"TopbarController@topbar_settings")->name('admin.topbar.settings');
        Route::post('/all',"TopbarController@update_topbar_settings");
        Route::post('/new-social-item','TopbarController@new_social_item')->name('admin.new.social.item');
        Route::post('/update-social-item','TopbarController@update_social_item')->name('admin.update.social.item');
        Route::post('/delete-social-item/{id}','TopbarController@delete_social_item')->name('admin.delete.social.item');
        Route::post('/settings/info-items',"TopbarController@update_topbar_info_items")->name('admin.topbar.info.item.store');
    });

    Route::group(['prefix'=>'appearance-settings/country-settings','namespace'=>'Admin'], function () {
        Route::get('/',"AdminCountryController@index")->name('admin.country');
        Route::post('/',"AdminCountryController@store");
        Route::post('/update','AdminCountryController@update')->name('admin.country.update');
        Route::post('/delete/{id}','AdminCountryController@delete')->name('admin.country.delete');
        Route::post('/bulk-action','AdminCountryController@bulk_action')->name('admin.country.bulk.action');
    });

    Route::group(['prefix'=>'general-settings','namespace'=>'Admin'], function () {
        Route::get('/site-identity', 'GeneralSettingsController@site_identity')->name('admin.general.site.identity');
        Route::post('/site-identity', 'GeneralSettingsController@update_site_identity');
        Route::get('/basic-settings', 'GeneralSettingsController@basic_settings')->name('admin.general.basic.settings');
        Route::post('/basic-settings', 'GeneralSettingsController@update_basic_settings');
        Route::get('/color-settings', 'GeneralSettingsController@color_settings')->name('admin.general.color.settings');
        Route::post('/color-settings', 'GeneralSettingsController@update_color_settings');
        Route::get('/seo-settings', 'GeneralSettingsController@seo_settings')->name('admin.general.seo.settings');
        Route::post('/seo-settings', 'GeneralSettingsController@update_seo_settings');
        Route::get('/scripts', 'GeneralSettingsController@scripts_settings')->name('admin.general.scripts.settings');
        Route::post('/scripts', 'GeneralSettingsController@update_scripts_settings');
        Route::get('/email-template', 'GeneralSettingsController@email_template_settings')->name('admin.general.email.template');
        Route::post('/email-template', 'GeneralSettingsController@update_email_template_settings');
        Route::get('/email-settings', 'GeneralSettingsController@email_settings')->name('admin.general.email.settings');
        Route::post('/email-settings', 'GeneralSettingsController@update_email_settings');
        Route::get('/typography-settings', 'GeneralSettingsController@typography_settings')->name('admin.general.typography.settings');
        Route::post('/typography-settings', 'GeneralSettingsController@update_typography_settings');
        Route::post('/typography-settings/single', 'GeneralSettingsController@get_single_font_variant')->name('admin.general.typography.single');
        Route::get('/cache-settings', 'GeneralSettingsController@cache_settings')->name('admin.general.cache.settings');
        Route::post('/cache-settings', 'GeneralSettingsController@update_cache_settings');
        Route::get('/page-settings', 'GeneralSettingsController@page_settings')->name('admin.general.page.settings');
        Route::post('/page-settings', 'GeneralSettingsController@update_page_settings');
        Route::get('/backup-settings', 'GeneralSettingsController@backup_settings')->name('admin.general.backup.settings');
        Route::post('/backup-settings', 'GeneralSettingsController@update_backup_settings');
        Route::post('/backup-settings/delete', 'GeneralSettingsController@delete_backup_settings')->name('admin.general.backup.settings.delete');
        Route::post('/backup-settings/restore', 'GeneralSettingsController@restore_backup_settings')->name('admin.general.backup.settings.restore');
        Route::get('/update-system', 'GeneralSettingsController@update_system')->name('admin.general.update.system');
        Route::post('/update-system', 'GeneralSettingsController@update_system_version');
        Route::get('/license-setting', 'GeneralSettingsController@license_settings')->name('admin.general.license.settings');
        Route::post('/license-setting', 'GeneralSettingsController@update_license_settings');
        Route::post('/license-setting-verify', 'GeneralSettingsController@license_key_generate')->name('admin.general.license.key.generate');
        Route::get('/update-check', 'GeneralSettingsController@update_version_check')->name('admin.general.update.version.check');
        Route::post('/download-update/{productId}/{tenant}', 'GeneralSettingsController@updateDownloadLatestVersion')->name('admin.general.update.download.settings');
        Route::get('/software-update-setting', 'GeneralSettingsController@software_update_check_settings')->name('admin.general.software.update.settings');
        Route::get('/custom-css', 'GeneralSettingsController@custom_css_settings')->name('admin.general.custom.css');
        Route::post('/custom-css', 'GeneralSettingsController@update_custom_css_settings');
        Route::get('/gdpr-settings', 'GeneralSettingsController@gdpr_settings')->name('admin.general.gdpr.settings');
        Route::post('/gdpr-settings', 'GeneralSettingsController@update_gdpr_cookie_settings');
        Route::get('/update-script', 'ScriptUpdateController@index')->name('admin.general.script.update');
        Route::post('/update-script', 'ScriptUpdateController@update_script');
        Route::get('/custom-js', 'GeneralSettingsController@custom_js_settings')->name('admin.general.custom.js');
        Route::post('/custom-js', 'GeneralSettingsController@update_custom_js_settings');
        Route::get('/regenerate-image', 'GeneralSettingsController@regenerate_image_settings')->name('admin.general.regenerate.thumbnail');
        Route::post('/regenerate-image', 'GeneralSettingsController@update_regenerate_image_settings');
        Route::get('/smtp-settings', 'GeneralSettingsController@smtp_settings')->name('admin.general.smtp.settings');
        Route::post('/smtp-settings', 'GeneralSettingsController@update_smtp_settings');
        Route::post('/smtp-settings/test', 'GeneralSettingsController@test_smtp_settings')->name('admin.general.smtp.settings.test');
        Route::get('/payment-settings', 'GeneralSettingsController@payment_settings')->name('admin.general.payment.settings');
        Route::post('/payment-settings', 'GeneralSettingsController@update_payment_settings');
        Route::get('/popup-settings', 'GeneralSettingsController@popup_settings')->name('admin.general.popup.settings');
        Route::post('/popup-settings', 'GeneralSettingsController@update_popup_settings');
        Route::get('/rss-settings', 'GeneralSettingsController@rss_feed_settings')->name('admin.general.rss.feed.settings');
        Route::post('/rss-settings', 'GeneralSettingsController@update_rss_feed_settings');
        Route::get('/sitemap-settings', 'GeneralSettingsController@sitemap_settings')->name('admin.general.sitemap.settings');
        Route::post('/sitemap-settings', 'GeneralSettingsController@update_sitemap_settings');
        Route::post('/sitemap-settings/delete', 'GeneralSettingsController@delete_sitemap_settings')->name('admin.general.sitemap.settings.delete');
        Route::get('/database-upgrade', 'GeneralSettingsController@database_upgrade')->name('admin.general.database.upgrade');
        Route::post('/database-upgrade', 'GeneralSettingsController@database_upgrade_post');
    });

    Route::group(['prefix'=>'languages','namespace'=>'Admin'], function () {
        Route::get('/', 'LanguageController@index')->name('admin.languages');
        Route::get('/words/frontend/{id}', 'LanguageController@frontend_edit_words')->name('admin.languages.words.frontend');
        Route::get('/words/backend/{id}', 'LanguageController@backend_edit_words')->name('admin.languages.words.backend');
        Route::post('/words/update/{id}', 'LanguageController@update_words')->name('admin.languages.words.update');
        Route::post('/new', 'LanguageController@store')->name('admin.languages.new');
        Route::post('/update', 'LanguageController@update')->name('admin.languages.update');
        Route::post('/delete/{id}', 'LanguageController@delete')->name('admin.languages.delete');
        Route::post('/default/{id}', 'LanguageController@make_default')->name('admin.languages.default');
        Route::post('/clone', 'LanguageController@clone_languages')->name('admin.languages.clone');
        Route::post('/add-new-string', 'LanguageController@add_new_string')->name('admin.languages.add.string');
        Route::post('/languages/regenerate-source-text','LanguageController@regenerate_source_text')->name('admin.languages.regenerate.source.texts');
    });

}); //End admin-home

/*----------------------------------------------------------------
 | BLOCKCHAIN MANAGEMENT ROUTES (ADMIN)
 |--------------------------------------------------------------*/
Route::group(['prefix' => 'admin-home/blockchain', 'namespace' => 'Admin', 'middleware' => ['setlang:backend','auth:admin','adminglobalVariable']], function () {
    Route::get('/', 'AdminBlockchainController@index')->name('admin.blockchain.all');
    Route::get('/view/{id}', 'AdminBlockchainController@view')->name('admin.blockchain.view');
    Route::get('/settings', 'AdminBlockchainController@settings')->name('admin.blockchain.settings');
    Route::post('/settings', 'AdminBlockchainController@updateSettings');
});

/*----------------------------------------------------------------
 | PATIENT WALLET VERIFICATION ROUTES (ADMIN)
 |--------------------------------------------------------------*/
Route::group(['prefix' => 'admin-home/patient-wallets', 'namespace' => 'Admin', 'middleware' => ['setlang:backend','auth:admin','adminglobalVariable']], function () {
    Route::get('/', 'PatientWalletController@index')->name('admin.patient.wallets');
    Route::post('/verify/{id}', 'PatientWalletController@verify')->name('admin.patient.wallet.verify');
    Route::post('/reject/{id}', 'PatientWalletController@reject')->name('admin.patient.wallet.reject');
});

/*----------------------------------------------------------------
 | FRAUD MANAGEMENT ROUTES (ADMIN)
 |--------------------------------------------------------------*/
Route::group(['prefix' => 'admin-home/fraud', 'namespace' => 'Admin', 'middleware' => ['setlang:backend','auth:admin','adminglobalVariable']], function () {
    Route::get('/dashboard', 'FraudController@dashboard')->name('admin.fraud.dashboard');
    Route::get('/reports', 'FraudController@index')->name('admin.fraud.reports');
    Route::get('/report/{id}', 'FraudController@view')->name('admin.fraud.view');
    Route::post('/review/{id}', 'FraudController@review')->name('admin.fraud.review');
    Route::post('/bulk-action', 'FraudController@bulkAction')->name('admin.fraud.bulk.action');
});

/*----------------------------------------------------------------
 | VERIFICATION MANAGEMENT ROUTES (ADMIN)
 |--------------------------------------------------------------*/
Route::group(['prefix' => 'admin-home/verifications', 'namespace' => 'Admin', 'middleware' => ['setlang:backend','auth:admin','adminglobalVariable']], function () {
    Route::get('/', 'VerificationController@index')->name('admin.verifications.all');
    Route::get('/view/{id}', 'VerificationController@view')->name('admin.verification.view');
    Route::post('/verify/{id}', 'VerificationController@verify')->name('admin.verification.verify');
    Route::post('/reject/{id}', 'VerificationController@reject')->name('admin.verification.reject');
    Route::post('/bulk-action', 'VerificationController@bulkAction')->name('admin.verification.bulk.action');
});

/*----------------------------------------------------------------
 | MILESTONE & ESCROW ROUTES (ADMIN)
 |--------------------------------------------------------------*/
Route::group(['prefix' => 'admin-home/campaigns/{campaignId}/milestones', 'namespace' => 'Admin', 'middleware' => ['setlang:backend','auth:admin','adminglobalVariable']], function () {
    Route::get('/', 'MilestoneController@index')->name('admin.milestones.index');
    Route::post('/', 'MilestoneController@store')->name('admin.milestones.store');
    Route::post('/{id}/proof', 'MilestoneController@submitProof')->name('admin.milestones.proof');
    Route::post('/{id}/verify', 'MilestoneController@verify')->name('admin.milestones.verify');
});

Route::post('admin-home/campaigns/{causeId}/verify-document', 'Admin\CausesController@verify_document')
    ->name('admin.campaign.verify_document');

Route::group(['prefix' => 'admin-home/campaigns/{campaignId}', 'namespace' => 'Admin', 'middleware' => ['setlang:backend', 'auth:admin', 'adminglobalVariable']], function () {
    Route::post('/refund-escrow', 'MilestoneController@refundEscrow')->name('admin.campaign.refund_escrow');
    Route::post('/freeze-escrow', 'MilestoneController@freezeEscrow')->name('admin.campaign.freeze_escrow');
    Route::post('/blacklist-wallet', 'MilestoneController@blacklistWallet')->name('admin.campaign.blacklist_wallet');
});

Route::get('admin-home/audit-logs', 'Admin\FraudController@auditLogs')
    ->middleware(['setlang:backend', 'auth:admin', 'adminglobalVariable'])
    ->name('admin.audit.logs');

Route::group(['middleware' => ['setlang:backend','auth:admin'],'prefix' => 'admin-home','namespace'=>'Admin'], function (){
    Route::post('/all', 'MediaUploadController@all_upload_media_file')->name('admin.upload.media.file.all');
    Route::post('/', 'MediaUploadController@upload_media_file')->name('admin.upload.media.file');
    Route::post('/chart', 'AdminDashboardController@get_chart_data')->name('admin.home.chat.data');
    Route::post('/chart/day', 'AdminDashboardController@get_chart_by_date_data')->name('admin.home.chat.data.by.day');
});
