<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;
use App\Dao\Models\Core\User;

/**
 * Class Payment
 *
 * @property $payment_id
 * @property $payment_code
 * @property $payment_tanggal
 * @property $payment_id_user
 * @property $payment_value
 * @property $payment_paid
 * @property $payment_url
 * @property $payment_done
 * @property $payment_method
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Payment extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'payment';
    protected $primaryKey = 'payment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['payment_id', 'payment_code', 'payment_tanggal', 'payment_id_user', 'payment_value', 'payment_paid', 'payment_url', 'payment_done', 'payment_method'];

    public static function field_name()
    {
        return 'payment_code';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function has_iuran()
    {
        return $this->belongsToMany(
            Iuran::class,
            "payment_iuran",
            "payment_id",
            "iuran_id",
        )->withPivot("iuran_harga", "token", 'tanggal');
    }

    public function has_user()
    {
        return $this->hasOne(User::class, 'id', 'payment_id_user');
    }

    public function dataRepository($selected = [], $relation = [])
    {
        $query = $this->select($this->getTable() . ".*")
            ->leftJoinRelationship("has_user")
            ->addSelect(['name']);

        if ($selected) {
            $query = $query->addSelect($selected);
        }

        $query = env("PAGINATION_SIMPLE")
            ? $query->simplePaginate(env("PAGINATION_NUMBER"))
            : $query->paginate(env("PAGINATION_NUMBER"));

        return $query;
    }
}
