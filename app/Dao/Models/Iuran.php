<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;


/**
 * Class Iuran
 *
 * @property $iuran_id
 * @property $iuran_nama
 * @property $iuran_tanggal
 * @property $iuran_bulanan
 * @property $iuran_visit
 * @property $iuran_type
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

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
    protected $fillable = ['iuran_id', 'iuran_nama', 'iuran_tanggal', 'iuran_harga', 'iuran_type', 'iuran_keterangan', 'iuran_token'];

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
