<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Models\Core\SystemModel;
use App\Dao\Models\Core\User;
use App\Facades\Model\CategoryModel;
use App\Facades\Model\JadwalModel;
use App\Facades\Model\JarakModel;
use App\Facades\Model\UserModel;

/**
 * Class Race
 *
 * @property $race_id
 * @property $race_jadwal_id
 * @property $race_user_id
 * @property $race_category_id
 * @property $race_tanggal
 * @property $race_waktu
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Race extends SystemModel
{
    protected $perPage = 20;
    protected $table = 'race';
    protected $primaryKey = 'race_id';

    protected $filters = [
        'filter',
        'id',
        'name',
        'race_id',
        'jarak_id',
        'jadwal_id',
        'start_date',
        'end_date',
    ];

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build(User::field_name())->name('Nama User'),
            DataBuilder::build('race_notes')->name('Keterangan')->sort(),
        ];
    }

     public function start_date($query)
    {
        $date = request()->get('start_date');
        if ($date) {
            $query = $query->whereDate('race_tanggal', '>=', $date);
        }

        return $query;
    }

    public function end_date($query)
    {
        $date = request()->get('end_date');

        if ($date) {
            $query = $query->whereDate('race_tanggal', '<=', $date);
        }

        return $query;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['race_id', 'race_jadwal_id', 'race_jarak_id', 'race_user_id', 'race_notes', 'race_tanggal', 'race_waktu'];

    public static function field_name()
    {
        return 'race_notes';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function has_jadwal()
    {
        return $this->hasOne(JadwalModel::getModel(), JadwalModel::field_primary(), 'race_jadwal_id');
    }

    public function has_jarak()
    {
        return $this->hasOne(JarakModel::getModel(), JarakModel::field_primary(), 'race_jarak_id');
    }

    public function has_user()
    {
        return $this->hasOne(UserModel::getModel(), UserModel::field_primary(), 'race_user_id');
    }

    public function dataRepository($selected = [], $relation = [])
    {
        $query = $this->select([
            $this->getTable().'.*',
            'id',
            'name',
            JadwalModel::getTableName().'.*',
            JarakModel::getTableName().'.*'
        ])
            ->leftJoinRelationship('has_jadwal')
            ->leftJoinRelationship('has_jarak')
            ->leftJoinRelationship('has_user');

        $query = $query
        ->filter();

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));

        return $query;
    }
}
