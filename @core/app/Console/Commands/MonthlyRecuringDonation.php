<?php

namespace App\Console\Commands;

use App\CauseLogs;
use App\Mail\DonationMessage;
use App\Recuring;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MonthlyRecuringDonation extends Command
{

    protected $signature = 'donation:recurring';
    protected $description = 'This is for recurring donation monthly';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $all_donations = \App\CauseLogs::where('status','complete')->get();

        foreach ($all_donations as $donation){

            foreach ($donation->recurings as $recuring_data){

                if(!empty($recuring_data->donation_log)){

                    $site_title = get_static_option('site_title');
                    $user_mail = optional($recuring_data->donation_log)->email;

                    $mail_send = get_static_option('how_many_days_ago_user_get_recuring_notification');

                    $expired_date = $recuring_data->expire_date;
                    $expired_before = \Carbon\Carbon::parse($expired_date)->subDays((int)$mail_send)->format("Y-m-d");
                    $today = Carbon::now()->format("Y-m-d");

                    if($today == $expired_before){
                        $customer_subject = sprintf(__('You have a monthly donation pending at %s') ,$site_title);

                        try{
                            Mail::to($user_mail)->send(new \App\Mail\DonationRecuring(optional($recuring_data->donation_log), $customer_subject));
                        }catch(\Exception $e){
                            //
                        }

                    }

                }
            }
        }

        return 0;
    }
}
