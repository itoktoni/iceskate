<?php
namespace App\Console\Commands;

use App\Dao\Enums\IuranType;
use App\Dao\Models\Core\User;
use App\Dao\Models\Iuran;
use App\Dao\Models\Payment;
use Illuminate\Console\Command;

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
        // Iuran bulanan aktif (tidak hardcode id, tidak tergantung view_payment)
        $iuran = Iuran::where('iuran_type', IuranType::BULANAN)
            ->where('iuran_aktif', 1)
            ->first(['iuran_id', 'iuran_harga', 'iuran_voucher']);

        if (empty($iuran)) {
            $this->error('Tidak ada iuran BULANAN aktif.');
            return;
        }

        $users = User::whereNotNull('phone')->get();
        $created = 0;

        foreach ($users as $user) {

            // Cek langsung ke tabel payment: sudah ada tagihan bulan ini?
            $exists = Payment::where('payment_id_user', $user->id)
                ->where('payment_iuran', $iuran->iuran_id)
                ->whereYear('payment_tanggal', now()->format('Y'))
                ->whereMonth('payment_tanggal', now()->format('m'))
                ->exists();

            if ($exists) {
                continue;
            }

            try {
                $code = unic(10) . date('Ymd');

                Payment::create([
                    'payment_id'      => $code,
                    'payment_tanggal' => now()->format('Y-m-d'),
                    'payment_id_user' => $user->id,
                    'payment_value'   => $iuran->iuran_harga,
                    'payment_paid'    => 0,
                    'payment_iuran'    => $iuran->iuran_id,
                    'payment_voucher'    => $iuran->iuran_voucher,
                ]);

                $created++;
            } catch (\Throwable $e) {
                $this->error('Gagal user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        $this->info('Selesai. Tagihan baru dibuat: ' . $created . ' dari ' . $users->count() . ' user.');
    }
}
