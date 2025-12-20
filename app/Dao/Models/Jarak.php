<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;


/**
 * Class Jarak
 *
 * @property $jarak_id
 * @property $jarak_nama
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Jarak extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'jarak';
    protected $primaryKey = 'jarak_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['jarak_id', 'jarak_nama', 'jarak_australia', 'jarak_asian', 'jarak_asian_open', 'jarak_asian_thropy'];

    public static function field_name()
    {
        return 'jarak_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }
}
