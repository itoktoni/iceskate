<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class PinjamType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    public const PINJAM = null;

    public const SELESAI = 'SELESAI';
}
