<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;
use App\Facades\Model\AssetModel;
use App\Facades\Model\UserModel;

/**
 * Class Pinjam
 *
 * @property $pinjam_id
 * @property $pinjam_tanggal
 * @property $pinjam_kembali
 * @property $pinjam_asset_id
 * @property $pinjam_user_id
 * @property $pinjam_qty
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Pinjam extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'pinjam';
    protected $primaryKey = 'pinjam_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['pinjam_id', 'pinjam_tanggal', 'pinjam_selesai','pinjam_catatan', 'pinjam_kembali', 'pinjam_asset_id', 'pinjam_user_id', 'pinjam_qty'];

    public static function field_name()
    {
        return 'pinjam_tanggal';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function dataRepository($selected = [], $relation = [])
    {
        $query = $this->select($this->getTable().'.*', 'name', 'asset_nama')
            ->leftJoinRelationship('has_asset')
            ->leftJoinRelationship('has_user')
            ->filter();

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));

        return $query;
    }

    public function has_user()
    {
        return $this->hasOne(UserModel::getModel(), UserModel::field_primary(), 'pinjam_user_id');
    }

    public function has_asset()
    {
        return $this->hasOne(AssetModel::getModel(), AssetModel::field_primary(), 'pinjam_asset_id');
    }
}
