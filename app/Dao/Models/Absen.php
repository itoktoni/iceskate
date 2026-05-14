<?php

namespace App\Dao\Models;

use App\Dao\Models\Core\SystemModel;

class Absen extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'absen';
    protected $primaryKey = 'code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['jadwal_id', 'id', 'payment', 'code', 'use_date', 'hadir'];


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
