<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Admin;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        /* admin-panel guard */
        $permissions = [
            'appearance-form-builder','appearance-media-image','appearance-menu-manage-create','appearance-menu-manage-delete',
            'appearance-menu-manage-edit','appearance-menu-manage-list','appearance-navbar-settings','appearance-topbar-settings',
            'appearance-widget-manage','blog-category-create','blog-category-delete','blog-category-edit','blog-category-list',
            'blog-create','blog-delete','blog-edit','blog-list','blog-page-settings','blog-single-page-settings',
            'client-area-create','client-area-delete','client-area-edit','client-area-list','counterup-create','counterup-delete',
            'counterup-edit','counterup-list','donation-category-create','donation-category-delete','donation-category-edit',
            'donation-category-list','donation-create','donation-delete','donation-edit','donation-gift-create',
            'donation-gift-delete','donation-gift-edit','donation-gift-list','donation-list','donation-payment-delete',
            'donation-payment-edit','donation-payment-list','donation-pending-cause','donation-settings',
            'donations-flag-report-delete','donations-flag-report-list','donations-flag-report-mail-send',
            'donations-flag-report-status-update','donations-flag-report-view','donation-withdraw-delete','donation-withdraw-edit',
            'donation-withdraw-list','donation-withdraw-view','event-attendance-create','event-attendance-delete',
            'event-attendance-edit','event-attendance-list','event-attendance-mail','event-attendance-report',
            'event-category-create','event-category-delete','event-category-edit','event-category-list','event-create',
            'event-delete','event-edit','event-list','event-payment-log-delete','event-payment-log-edit',
            'event-payment-log-list','event-payment-log-report','event-payment-log-view','event-settings','event-single-settings',
            'faq-create','faq-delete','faq-edit','faq-list','general-settings-basic-settings','general-settings-cache-settings',
            'general-settings-color-settings','general-settings-custom-css','general-settings-custom-js',
            'general-settings-database-upgrade','general-settings-email-template','general-settings-gdpr-settings',
            'general-settings-license','general-settings-page-settings','general-settings-payment-gateway',
            'general-settings-regenerate-media-image','general-settings-rss-feed','general-settings-seo-settings',
            'general-settings-site-identity','general-settings-sitemap','general-settings-smtp-settings',
            'general-settings-third-party-script','general-settings-typography','home_variant',
            'image-gallery-category-create','image-gallery-category-delete','image-gallery-category-edit',
            'image-gallery-category-list','image-gallery-create','image-gallery-delete','image-gallery-edit',
            'image-gallery-list','image-gallery-page-settings','job-applicant-delete','job-applicant-list',
            'job-applicant-mail','job-applicant-report','job-applicant-view','job-category-create','job-category-delete',
            'job-category-edit','job-category-list','job-create','job-delete','job-edit','job-list','job-settings',
            'language-create','language-delete','language-edit','language-list','mobile-slider-create','mobile-slider-delete',
            'mobile-slider-edit','mobile-slider-list','newsletter-create','newsletter-delete','newsletter-list',
            'newsletter-mail-send','page-create','page-delete','page-edit','page-list','page-settings-about-page-manage',
            'page-settings-contact-page-manage','page-settings-error-page-manage','page-settings-event-page-manage',
            'page-settings-maintain-page-manage','page-settings-success-story-page-manage','register-page-manage',
            'reward-create','reward-delete','reward-edit','reward-list','reward-redeem-delete','reward-redeem-edit',
            'reward-redeem-list','reward-redeem-view','success-story-category-create','success-story-category-delete',
            'success-story-category-edit','success-story-category-list','success-story-create','success-story-delete',
            'success-story-edit','success-story-list','support-ticket-category-create','support-ticket-category-delete',
            'support-ticket-category-edit','support-ticket-category-index','support-ticket-create','support-ticket-delete',
            'support-ticket-index','support-ticket-page-settings','support-ticket-view','team-member-create',
            'team-member-delete','team-member-edit','team-member-list','testimonial-create','testimonial-delete',
            'testimonial-edit','testimonial-list','user-create','user-delete','user-edit','user-list','user-tax-delete',
            'user-tax-list',

            /* MediFund blockchain/fraud additions */
            'blockchain-manage','fraud-reports-view','fraud-reports-resolve','patient-wallet-verify',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'admin');
        }

        $super = Role::findOrCreate('Super Admin', 'admin');
        $super->syncPermissions(Permission::where('guard_name', 'admin')->get());
        $staff = Role::findOrCreate('Admin', 'admin');
        $staff->syncPermissions([
            'donation-list','donation-create','donation-edit','donation-pending-cause','donation-payment-list',
            'donations-flag-report-list','donations-flag-report-view','donations-flag-report-status-update',
            'fraud-reports-view','fraud-reports-resolve','patient-wallet-verify','user-list','user-edit',
            'donation-withdraw-list','donation-withdraw-view',
        ]);

        $admin = Admin::where('email', 'admin@medifund.test')->first();
        if ($admin && !$admin->hasRole($super)) {
            $admin->assignRole($super);
        }
    }
}
