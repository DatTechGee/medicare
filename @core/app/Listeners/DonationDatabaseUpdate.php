<?php

namespace App\Listeners;

use App\Cause;
use App\CauseLogs;
use App\Events\DonationSuccess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DonationDatabaseUpdate
{

    public function __construct()
    {
        //
    }

    public function handle(DonationSuccess $event)
    {
        if (empty($event->data) && !isset($event->data['transaction_id'])){return;}

        //update donation log status/transaction id

        $payment_log_details = CauseLogs::findOrFail($event->data['donation_log_id']);
        $payment_log_details->status = ( $payment_log_details->payment_gateway === 'manual_payment') ? 'pending' : 'complete';
        $payment_log_details->transaction_id = $event->data['transaction_id'];
        $payment_log_details->save();

        $event_details = Cause::find($payment_log_details->cause_id);
        //update donation raised amount
        $event_details->raised = (int) $event_details->raised + (int) $payment_log_details->amount;
        $event_details->save();

        // hold completed gateway donations in platform escrow (net of the platform fee)
        if ($payment_log_details->status === 'complete') {
            $owner_fee = \App\Helpers\DonationHelpers::get_donation_charge_for_campaign_owner($payment_log_details->amount);
            if ((float) ($payment_log_details->admin_charge ?? 0) <= 0 && $owner_fee > 0) {
                $payment_log_details->admin_charge = round($owner_fee, 2);
                $payment_log_details->save();
            }
            \App\Escrow::firstOrCreate(
                ['cause_log_id' => $payment_log_details->id],
                [
                    'campaign_id' => $event_details->id,
                    'amount' => round(max((float) $payment_log_details->amount - (float) ($payment_log_details->admin_charge ?? 0), 0), 2),
                    'donor_wallet_address' => $payment_log_details->donor_wallet_address,
                    'blockchain_tx_hash' => $payment_log_details->blockchain_transaction_hash,
                    'status' => 'held',
                ]
            );
        }
    }
}
