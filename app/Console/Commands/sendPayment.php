<?php

namespace App\Console\Commands;

use App\Dao\Models\Payment;
use App\Services\InvoiceService;
use Illuminate\Console\Command;

class sendPayment extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'send:payment';

    /**
     * The console command description.
     */
    protected $description = 'Send payment reminders via WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payment = Payment::select(['payment.*', 'iuran.*', 'users.*'])
            ->leftJoinRelationship('has_iuran')
            ->leftJoinRelationship('has_user')
            ->where('payment_tanggal', now()->format('Y-m-d'))
            ->where('payment_paid', 0)
            ->whereNull('payment_sent')
            ->get();

        foreach ($payment as $data) {

            InvoiceService::generate($data->payment_id);
        }
    }
}
