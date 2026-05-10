<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;

class Token extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'view_token';
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
        'payment_tanggal',
        'payment_id_user',
        'payment_iuran',
        'payment_voucher',
        'used',
        'total',
    ];

    public static function field_name()
    {
        return 'payment_tanggal';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function dataRepository($selected = [], $relation = [])
    {
        $query = $this->select($this->getTable().'.*', 'name')
            ->leftJoinRelationship('has_user');

        if($selected)
        {
            $query = $query->addSelect($selected);
        }

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));

        return $query;
    }

}