<?php
namespace App\Console\Commands;

use App\Dao\Models\Core\User;
use App\Dao\Models\Iuran;
use App\Dao\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreatePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNotNull('phone')->get();

        foreach ($users as $user) {

            $sent = DB::table('view_payment')
                ->where('id', $user->id)
                ->where('payment_iuran', 1)
                ->whereYear('payment_tanggal', now()->format('Y'))
                ->whereMonth('payment_tanggal', now()->format('m'))
                ->count();

                info($sent);

            if ($sent == 0) {
                $iuran = Iuran::find(1, ['iuran_harga', 'iuran_voucher']);
                $harga = $iuran->iuran_harga;
                $token = $iuran->iuran_voucher;

                $code    = unic(10) . date('Ymd');

                $insert = [
                    'payment_id'      => $code,
                    'payment_tanggal' => now()->format('Y-m-d'),
                    'payment_id_user' => $user->id,
                    'payment_value'   => $harga,
                    'payment_paid'    => 0,
                    'payment_iuran'    => 1,
                    'payment_voucher'    => $token,
                ];

                Payment::create($insert);

                sleep(3);
            }
        }

    }
}
