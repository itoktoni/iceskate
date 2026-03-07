<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;


/**
 * Class Absen
 *
 * @property $jadwal_id
 * @property $id
 * @property $payment
 * @property $code
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Payment extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'payment';
    protected $primaryKey = 'payment_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'payment_id' => 'string',
    ];

    protected $filters = [
        'filter',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'payment_code',
        'payment_url',
        'payment_tanggal',
        'payment_done',
        'payment_method',
        'payment_paid',
        'payment_id_user',
        'payment_value',
    ];

    public static function field_name()
    {
        return 'jadwal_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function dataRepository($selected = [], $relation = [])
    {
        $query = $this->select($this->getTable().'.*');

        if($selected)
        {
            $query = $query->addSelect($selected);
        }

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));

        return $query;
    }

    public function has_iuran()
    {
        return $this->belongsToMany(Iuran::class, 'payment_iuran', 'payment_id', 'iuran_id')->withPivot('iuran_harga');
    }
}