<?php

namespace App\Dao\Enums\Core;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class RoleType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    public const Admin = 'admin';

    public const User = 'user';
}
