<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class IuranType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    public const BULANAN = 'BULANAN';
    public const VISIT = 'VISIT';
    public const EVENT = 'EVENT';

}
