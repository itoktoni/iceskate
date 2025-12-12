<?php

namespace App\Dao\Enums\Core;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class MemberType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    public const TRIAL = 'TRIAL';

    public const MEMBER = 'MEMBER';
}
