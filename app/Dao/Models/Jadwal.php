<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;
use App\Dao\Models\Core\User;
use App\Facades\Model\CategoryModel;
use App\Facades\Model\UserModel;

/**
 * Class Jadwal
 *
 * @property $jadwal_id
 * @property $jadwal_nama
 * @property $jadwal_tanggal
 * @property $jadwal_keterangan
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Jadwal extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'jadwal';
    protected $primaryKey = 'jadwal_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['jadwal_id', 'jadwal_category_id', 'jadwal_nama', 'jadwal_tanggal', 'jadwal_keterangan'];

     public static function field_name()
    {
        return 'jadwal_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function has_absen()
    {
        return $this->belongsToMany(User::class, 'absen', 'jadwal_id', 'id');
    }

    public function has_category()
    {
        return $this->hasOne(CategoryModel::getModel(), CategoryModel::field_primary(), 'jadwal_category_id');
    }

    public function dataRepository($selected = [], $relation = [])
    {
        $query = $this->select($this->getTable().'.*', 'category_nama')
            ->leftJoinRelationship('has_category')
            ->filter();

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));

        return $query;
    }
}
