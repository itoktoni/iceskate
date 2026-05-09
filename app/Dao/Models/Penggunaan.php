<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;


/**
 * Class Penggunaan
 *
 * @property $penggunaan_id
 * @property $penggunaan_tanggal
 * @property $penggunaan_id_iuran
 * @property $penggunaan_id_jadwal
 * @property $penggunaan_created_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Penggunaan extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'penggunaan';
    protected $primaryKey = 'penggunaan_code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['penggunaan_code', 'penggunaan_tanggal', 'penggunaan_id_iuran', 'penggunaan_id_jadwal', 'penggunaan_created_at'];

    public static function field_name()
    {
        return 'penggunaan_code';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }
}
