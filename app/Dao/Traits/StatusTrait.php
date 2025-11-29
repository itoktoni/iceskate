<?php

namespace App\Dao\Traits;

trait StatusTrait
{
    public static function getOptions($value = false): array
    {
        $collect = collect(self::getInstances());

        if ($value && is_array($value)) {
            $collect = $collect->whereIn('value', $value);
        } elseif ($value && is_int($value)) {
            $collect = $collect->where('value', $value);
        }

        $data = [];
        foreach ($collect as $item) {
            $data[$item->value] = $item->description;
        }

        return $data;
    }

    public static function getApi($value = false): array
    {
        $collect = collect(self::getInstances());

        if ($value && is_array($value)) {
            $collect = $collect->whereIn('value', $value);
        } elseif ($value && is_int($value)) {
            $collect = $collect->where('value', $value);
        }

        $data = [];
        foreach ($collect as $item) {
            $data[] = ['id' => $item->value, 'name' => $item->description];
        }

        return $data;
    }
}
