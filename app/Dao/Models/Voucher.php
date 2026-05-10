<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;

class Voucher extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'view_voucher';
    protected $primaryKey = 'code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id_user',
        'name',
        'payment_id',
        'payment_tanggal',
        'iuran_id',
        'iuran_nama',
        'payment_voucher',
        'code',
        'use_date',
        'jadwal_nama',
        'jadwal_tanggal',
        'jadwal_keterangan ',
    ];


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
}
