<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;
use App\Facades\Model\UserModel;

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
        'payment_tanggal',
        'payment_id_user',
        'payment_value',
        'payment_paid',
        'payment_url',
        'payment_done',
        'payment_method',
        'payment_iuran',
        'payment_voucher',
        'payment_wa',
        'payment_sent',
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

    public function has_iuran()
    {
        return $this->belongsToMany(Iuran::class, 'payment_iuran', 'payment_id', 'iuran_id')->withPivot('iuran_harga');
    }

    public function has_user()
    {
        return $this->hasOne(UserModel::getModel(), UserModel::field_primary(), 'payment_id_user');
    }
}