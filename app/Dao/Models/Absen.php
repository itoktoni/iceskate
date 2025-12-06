<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;

/**
 * Class absen
 *
 * @property $absen_id
 * @property $absen_name
 * @property $absen_user_id
 * @property User $user
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Absen extends SystemModel
{
    protected $perPage = 20;

    protected $table = 'absen';

    protected $primaryKey = 'absen_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['absen_id', 'absen_nama'];

    public static function field_name()
    {
        return 'absen_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }
}
