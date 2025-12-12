<?php

namespace App\Models;

use Corcel\Model\Page as Corcel;
use Tbruckmaier\Corcelacf\AcfTrait;

class Page extends Corcel
{
     use AcfTrait;
     protected $postType = 'page'; // Explicitly define the post type

    public static function boot()
    {
        self::addAcfRelations(['template']);
        parent::boot();
    }
}