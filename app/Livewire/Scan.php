<?php

namespace App\Livewire;

use App\Dao\Models\Absen;
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;

class Scan extends Component
{
    public $scan = '';
    public $result = '';
    public $error = '';

    protected $rules = [
        'scan' => 'required|string',
    ];

    public function render()
    {
        return view('livewire.scan');
    }

    public function search()
    {
        $this->validate([
            'scan' => 'required|string',
        ]);

        $this->result = null;

        try {
            $decrypt = $encrypted = Crypt::decryptString($this->scan);
            $decode = json_decode($decrypt);

            $user_id = $decode->u;
            $jadwal_id = $decode->j;
            $payment_id = $decode->p;
            $total = $decode->t;

            // $decode = explode('#', $decrypt);

            // $user_id = $decode[0];
            // $jadwal_id = $decode[1];
            // $payment_id = $decode[2];
            // $total = $decode[3];

            if($total == 0)
            {
                $this->error = 'Voucher sudah habis !';
                return;
            }

            $search = Absen::where('jadwal_id', $jadwal_id)->where('id', $user_id)->first();

            if(!empty($search))
            {
                if(!empty($search->use_code))
                {
                    $this->error = 'Voucher sudah terpakai !';
                }
                else
                {
                    Absen::where('jadwal_id', $jadwal_id)
                    ->where('id', $user_id)
                    ->update([
                        'payment' => $payment_id,
                        'code' => unic(10),
                        'use_date' => date('Y-m-d')
                    ]);

                    $this->error = 'Success Record !';
                }
            }
            else
            {
                Absen::create([
                    'jadwal_id' => $jadwal_id,
                    'id' => $user_id,
                    'payment' => $payment_id,
                    'code' => unic(10),
                    'use_date' => date('Y-m-d')
                ]);

                $this->error = 'Success Record !';
            }

            $this->scan = '';

        } catch (\Throwable $th) {
            $this->error = 'Barcode is Not valid !';
        }

    }
}
