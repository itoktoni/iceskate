<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;


/**
 * Class Absen
 *
 * @property $jadwal_id
 * @property $id
 * @property $payment
 * @property $code
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Absen extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'absen';
    protected $primaryKey = 'jadwal_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['jadwal_id', 'id', 'payment', 'code', 'payment_date'];


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
