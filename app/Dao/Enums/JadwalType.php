<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class JadwalType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    public const LATIHAN = 'LATIHAN';
    public const LOMBA = 'LOMBA';

}
