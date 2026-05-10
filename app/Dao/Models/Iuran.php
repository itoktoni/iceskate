<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;


class Iuran extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'iuran';
    protected $primaryKey = 'iuran_id';

    protected $casts = [
        // 'iuran_tanggal' => 'date',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['iuran_id', 'iuran_nama', 'iuran_tanggal', 'iuran_harga', 'iuran_type', 'iuran_keterangan', 'iuran_aktif', 'iuran_voucher'];

    public static function field_name()
    {
        return 'iuran_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function has_payment()
    {
        return $this->belongsToMany(Payment::class, 'payment_iuran', 'iuran_id', 'payment_id');
    }
}
