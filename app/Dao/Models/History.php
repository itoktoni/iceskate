<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;

/**
 * Class Category
 *
 * @property $category_id
 * @property $category_name
 * @property $category_user_id
 * @property User $user
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class History extends SystemModel
{
    protected $perPage = 20;

    protected $table = 'history';

    protected $primaryKey = 'payment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['payment_id', 'payment_code'];

    public static function field_name()
    {
        return 'payment_id';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }
}
