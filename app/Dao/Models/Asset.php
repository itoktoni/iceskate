<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;
use App\Facades\Model\PinjamModel;

/**
 * Class Asset
 *
 * @property $asset_id
 * @property $asset_nama
 * @property $asset_qty
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Asset extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'asset';
    protected $primaryKey = 'asset_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['asset_id', 'asset_nama', 'asset_keterangan', 'asset_qty'];

    public static function field_name()
    {
        return 'asset_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function has_pinjam()
    {
        return $this->hasMany(PinjamModel::getModel(), 'pinjam_asset_id', Asset::field_primary());
    }

}
