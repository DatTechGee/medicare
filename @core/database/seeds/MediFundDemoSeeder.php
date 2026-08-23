<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MediFundDemoSeeder extends Seeder
{
    public function run()
    {
        /* ---------- make re-runs idempotent ---------- */
        \DB::statement('PRAGMA foreign_keys = OFF');
        foreach (['cause_logs', 'causes', 'cause_categories', 'media_uploads'] as $t) {
            \DB::table($t)->delete();
        }
        \DB::table('admins')->where('email', 'admin@medifund.test')->delete();
        \DB::table('users')->whereIn('email', ['patient@medifund.test', 'donor@medifund.test'])->delete();
        \DB::statement('PRAGMA foreign_keys = ON');

        /* ---------- static options (site config) ---------- */
        $options = [
            'site_title' => 'MediFund',
            'site_tag_line' => 'Blockchain-Powered Medical Crowdfunding',
            'site_meta_description' => 'Verified medical campaigns, on-chain escrow, fraud-screened fundraising.',
            'site_global_email' => 'admin@medifund.test',
            'site_global_currency' => 'USD',
            'site_currency_symbol' => '$',
            'site_currency_icon_position' => 'left',
            'site_default_payment_gateway' => 'blockchain',
            'site_payment_gateway' => json_encode(['blockchain']),
            'site_color' => '#627EEA',
            'site_main_color_two' => '#00D4AA',
            'site_heading_color' => '#EAECF5',
            'site_paragraph_color' => '#9AA3BF',
            'home_page_variant' => '07',
            'donation_page_slug' => 'donation',
            'donation_single_page_variant' => '',
            'donation_login_user_donate_show_hide' => '',
            'donation_button_text' => 'Donate Now',
            'releated_donation_text' => 'Related Campaigns',
            'cause_single_donate_button_text' => 'Donate Now',
            'home_page_navbar_search_show_hide' => 'on',
            'home_page_recent_cause_section_status' => 'on',
            'home_page_feature_cause_section_status' => 'on',
            'home_page_cause_category_section_status' => 'on',
            'home_page_counterup_section_status' => 'on',
            'home_page_header_slider_section_status' => '',
            'site_sticky_navbar_enabled' => 'on',
            'site_rtl_enabled' => '',
            'site_gdpr_cookie_enabled' => '',

            /* blockchain settings — mirror blockchain/deployments.json */
            'blockchain_demo_mode' => '0',
            'blockchain_network_name' => 'MediFund Local Network',
            'blockchain_chain_id' => '31337',
            'blockchain_rpc_url' => 'http://127.0.0.1:8545',
            'blockchain_explorer_url' => '/blockchain/explorer',
            'blockchain_contract_address' => '0x5FbDB2315678afecb367f032d93F642f64180aa3',
            'blockchain_donation_contract_address' => '0x9fE46736679d2D9a65F0992F2272dE9f3c7fa6e0',
            'blockchain_escrow_contract_address' => '0xe7f1725E7734CE288F8367e1Bb143E90bb3F0512',
            'blockchain_verification_contract_address' => '0xCf7Ed3AccA5a467e9e704C703E8D87F634fB0Fc9',
            'blockchain_min_donation' => '1',
            'blockchain_max_donation' => '100000',
            'blockchain_eth_usd_rate' => '3450',
            'blockchain_transfer_mode' => 'real',
            'site_receiving_wallet' => '0x80354450F4c300F178de2Ab718AbA6D2818CE102',

            /* fraud engine */
            'fraud_auto_approve_threshold' => '20',
            'fraud_block_threshold' => '50',
        ];
        foreach ($options as $name => $value) {
            \App\StaticOption::updateOrCreate(['option_name' => $name], ['option_value' => $value]);
        }

        /* generic defaults for every other option key referenced by the theme */
        $knownKeys = [
            'about_page_name','about_page__meta_tags','about_page_meta_description','contact_page_name','contact_page__meta_tags',
            'faq_page_name','faq_page__meta_tags','blog_page_name','blog_page__meta_tags','events_page_name','events_page__meta_tags',
            'service_page_name','service_page__meta_tags','privacy_policy_page_name','terms_and_condition_page_name','career_page_name',
            'donation_page_name','donation_page__meta_tags','product_page_name','gallery_page_name','job_page_name','team_page_name',
            'top_bar_show_hide','navbar_show_hide','footer_copyright_text','site_favicon','site_logo','maintain_page_logo',
        ];
        foreach ($knownKeys as $key) {
            \App\StaticOption::firstOrCreate(['option_name' => $key], ['option_value' => '']);
        }

        /* ---------- default language (required by GlobalVariableMiddleware) ---------- */
        \DB::table('languages')->delete();
        \DB::table('languages')->insert([
            'name' => 'English', 'slug' => 'en', 'direction' => 'ltr',
            'status' => 'publish', 'default' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        /* ---------- admin ---------- */
        $adminId = \DB::table('admins')->insertGetId([
            'name' => 'MediFund Admin',
            'email' => 'admin@medifund.test',
            'username' => 'medifund_admin',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /* ---------- users (patient payout wallet = owner's MetaMask account) ---------- */
        $receivingWallet = env('MEDIFUND_RECEIVING_WALLET', '0x80354450F4c300F178de2Ab718AbA6D2818CE102');
        $patientId = \DB::table('users')->insertGetId([
            'name' => 'Rafiq Islam', 'email' => 'patient@medifund.test', 'username' => 'rafiq_patient',
            'password' => Hash::make('password'), 'email_verified' => 1, 'status' => 'active',
            'role' => 'patient',
            'wallet_address' => $receivingWallet,
            'wallet_verified' => 1, 'wallet_verified_at' => now(),
            'campaign_permission' => 'on',
            'demo_eth_balance' => 100,
            'created_at' => now()->subDays(40), 'updated_at' => now(),
        ]);
        $donorId = \DB::table('users')->insertGetId([
            'name' => 'Amina Rahman', 'email' => 'donor@medifund.test', 'username' => 'amina_donor',
            'password' => Hash::make('password'), 'email_verified' => 1, 'status' => 'active',
            'wallet_address' => '0x15d34AAf54267DB7D7c367839AAf71A00a2C6A65',
            'demo_eth_balance' => 50,
            'created_at' => now()->subDays(120), 'updated_at' => now(),
        ]);

        /* ---------- campaign images ---------- */
        $imageIds = [];
        for ($i = 1; $i <= 6; $i++) {
            $imageIds[$i] = \DB::table('media_uploads')->insertGetId([
                'title' => "MediFund Demo Image 0$i",
                'alt' => "MediFund demo image $i",
                'path' => "medifund-demo-0$i.svg",
                'dimensions' => '800x520',
                'size' => filesize(public_path("assets/uploads/media-uploader/medifund-demo-0$i.svg")),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /* ---------- categories ---------- */
        $cats = ['Cardiac Care','Neurology','Oncology','Orthopedics','Dialysis & Transplant','Emergency Trauma'];
        $catIds = [];
        foreach ($cats as $idx => $name) {
            $catIds[] = \DB::table('cause_categories')->insertGetId([
                'title' => $name,
                'description' => "Verified $name fundraising campaigns.",
                'image' => $imageIds[($idx % 6) + 1],
                'status' => 'publish',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /* ---------- campaigns ----------
         * verification_status vocabulary from the Algorithm 1 gate:
         * approved | pending | rejected ; high risk => status draft
         */
        $causes = [
            [ // low risk / approved
                'title' => 'Emergency Open-Heart Surgery for Rafiq Islam',
                'slug' => 'emergency-open-heart-surgery-for-rafiq-islam',
                'excerpt' => 'A 46-year-old father of three needs urgent bypass surgery at Square Hospital. Every donation is held in smart-contract escrow until hospital invoices are released.',
                'amount' => 12500, 'raised' => 8420,
                'fraud_score' => 10, 'verification_status' => 'approved', 'status' => 'publish',
                'featured' => 'on', 'emmergency' => 'on',
                'hospital' => 'Square Hospital Ltd', 'patient' => 'Rafiq Islam',
                'wallet' => env('MEDIFUND_RECEIVING_WALLET', '0x80354450F4c300F178de2Ab718AbA6D2818CE102'), 'user_id' => $patientId,
                'img' => 1, 'cat' => 0,
            ],
            [ // low risk / approved
                'title' => 'Brain Tumor Treatment for Little Arif',
                'slug' => 'brain-tumor-treatment-for-little-arif',
                'excerpt' => 'Seven-year-old Arif requires proton-beam therapy abroad. Funds release in milestones verified by the treating oncology department.',
                'amount' => 40000, 'raised' => 26500,
                'fraud_score' => 15, 'verification_status' => 'approved', 'status' => 'publish',
                'featured' => 'on', 'emmergency' => null,
                'hospital' => 'National Institute of Neurosciences', 'patient' => 'Arif Hossain',
                'wallet' => env('MEDIFUND_RECEIVING_WALLET', '0x80354450F4c300F178de2Ab718AbA6D2818CE102'), 'user_id' => $patientId,
                'img' => 2, 'cat' => 1,
            ],
            [ // medium risk / pending review
                'title' => 'Kidney Dialysis Fund for Karim Mia',
                'slug' => 'kidney-dialysis-fund-for-karim-mia',
                'excerpt' => 'Ongoing dialysis support for a retired schoolteacher. Hospital registry match found — awaiting admin confirmation of the receiving wallet.',
                'amount' => 9000, 'raised' => 3100,
                'fraud_score' => 35, 'verification_status' => 'pending', 'status' => 'publish',
                'featured' => null, 'emmergency' => null,
                'hospital' => 'Dhaka Medical College Hospital', 'patient' => 'Karim Mia',
                'wallet' => env('MEDIFUND_RECEIVING_WALLET', '0x80354450F4c300F178de2Ab718AbA6D2818CE102'), 'user_id' => $patientId,
                'img' => 3, 'cat' => 4,
            ],
            [ // approved
                'title' => 'Spinal Cord Surgery for Nusrat Jahan',
                'slug' => 'spinal-cord-surgery-for-nusrat-jahan',
                'excerpt' => 'Post-accident spinal reconstruction surgery. Goal benchmarked against comparable orthopedic campaigns and cleared by the fraud engine.',
                'amount' => 18000, 'raised' => 15900,
                'fraud_score' => 5, 'verification_status' => 'approved', 'status' => 'publish',
                'featured' => null, 'emmergency' => 'on',
                'hospital' => 'Apollo Hospitals Dhaka', 'patient' => 'Nusrat Jahan',
                'wallet' => env('MEDIFUND_RECEIVING_WALLET', '0x80354450F4c300F178de2Ab718AbA6D2818CE102'), 'user_id' => $patientId,
                'img' => 4, 'cat' => 3,
            ],
            [ // approved
                'title' => 'Breast Cancer Chemotherapy Cycle for Salma Khatun',
                'slug' => 'breast-cancer-chemotherapy-cycle-for-salma-khatun',
                'excerpt' => 'Six-cycle chemotherapy plan at a registered oncology center. Document hashes are sealed on-chain and verifiable by any donor.',
                'amount' => 15000, 'raised' => 5200,
                'fraud_score' => 18, 'verification_status' => 'approved', 'status' => 'publish',
                'featured' => null, 'emmergency' => null,
                'hospital' => 'Evercare Hospital Dhaka', 'patient' => 'Salma Khatun',
                'wallet' => env('MEDIFUND_RECEIVING_WALLET', '0x80354450F4c300F178de2Ab718AbA6D2818CE102'), 'user_id' => $patientId,
                'img' => 5, 'cat' => 2,
            ],
            [ // HIGH RISK — auto-flagged to draft by the fraud gate
                'title' => 'URGENT!!! 500% MATCH DONATE NOW LIVER TRANSPLANT',
                'slug' => 'urgent-liver-transplant-flagged-demo',
                'excerpt' => 'Kept as a draft: the fraud engine flagged urgency spam, an unverified wallet and an implausible goal. Shown here so reviewers can see the full pipeline.',
                'amount' => 250000, 'raised' => 0,
                'fraud_score' => 75, 'verification_status' => 'pending', 'status' => 'draft',
                'featured' => null, 'emmergency' => 'on',
                'hospital' => '', 'patient' => 'Unnamed Patient',
                'wallet' => env('MEDIFUND_RECEIVING_WALLET', '0x80354450F4c300F178de2Ab718AbA6D2818CE102'), 'user_id' => $donorId,
                'img' => 6, 'cat' => 5,
            ],
        ];

        foreach ($causes as $c) {
            $cid = \DB::table('causes')->insertGetId([
                'cause_update_id' => 0,
                'title' => $c['title'],
                'slug' => $c['slug'],
                'excerpt' => $c['excerpt'],
                'cause_content' => '<p>'.$c['excerpt'].'</p><p>All donations for this campaign are routed through the MediFund smart-contract escrow. Donors can audit every contribution on-chain, and funds are only released against verified hospital invoices.</p>',
                'amount' => $c['amount'],
                'raised' => $c['raised'],
                'status' => $c['status'],
                'image' => $imageIds[$c['img']],
                'categories_id' => $catIds[$c['cat']],
                'user_id' => $c['user_id'],
                'admin_id' => null,
                'created_by' => 'user',
                'deadline' => now()->addDays(90)->toDateString(),
                'featured' => $c['featured'],
                'emmergency' => $c['emmergency'],
                'fraud_score' => $c['fraud_score'],
                'verification_status' => $c['verification_status'],
                'patient_name' => $c['patient'],
                'hospital_name' => $c['hospital'],
                'medical_details' => 'Treatment plan verified against national hospital registry.',
                'wallet_address' => $c['wallet'],
                'wallet_verified' => $c['verification_status'] === 'approved' ? 1 : 0,
                'document_hash' => hash('sha256', $c['slug']),
                'document_hashed_at' => now(),
                'meta_title' => $c['title'],
                'meta_description' => $c['excerpt'],
                'created_at' => now()->subDays(rand(3, 30)),
                'updated_at' => now(),
            ]);

            /* verification records surfaced in Admin > Verifications */
            $verStatus = $c['verification_status'] === 'approved' ? 'verified'
                : ($c['status'] === 'draft' && $c['fraud_score'] >= 50 ? 'rejected' : 'pending');
            foreach (['patient', 'hospital', 'document', 'amount'] as $vtype) {
                \DB::table('verifications')->insert([
                    'campaign_id' => $cid,
                    'type' => $vtype,
                    'status' => $verStatus,
                    'verified_by' => $verStatus === 'verified' ? 1 : null,
                    'notes' => $verStatus === 'verified'
                        ? ucfirst($vtype).' checks passed (Algorithm 1 + admin review)'
                        : ($verStatus === 'rejected'
                            ? ucfirst($vtype).' check failed — fraud score '.$c['fraud_score'].'/100'
                            : ucfirst($vtype).' awaiting admin review'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            /* donation history so cards/progress bars look alive */
            if ($c['raised'] > 0 && $c['status'] === 'publish') {
                $splits = [0.45, 0.3, 0.25];
                foreach ($splits as $k => $share) {
                    \DB::table('cause_logs')->insert([
                        'cause_id' => $cid,
                        'email' => 'donor'.$k.'@medifund.test',
                        'name' => $k === 2 ? 'Anonymous Donor' : ['James Cooper','Farhana Akter'][$k],
                        'status' => 'complete',
                        'amount' => round($c['raised'] * $share, 2),
                        'transaction_id' => 'MF-DEMO-'.strtoupper(\Str::random(8)),
                        'payment_gateway' => 'blockchain',
                        'track' => \Str::random(18),
                        'user_id' => $k === 0 ? $donorId : null,
                        'anonymous' => $k === 2 ? 1 : 0,
                        'donor_wallet_address' => $k === 0 ? '0x15d34AAf54267DB7D7c367839AAf71A00a2C6A65' : null,
                        'blockchain_transaction_hash' => null,
                        'payment_type' => 'one_time',
                        'donation_status' => 'confirmed',
                        'added_in_raised_amount' => 1,
                        'created_at' => now()->subDays(rand(1, 25)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
