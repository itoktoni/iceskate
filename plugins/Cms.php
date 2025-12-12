<?php
namespace Plugins;

use Corcel\Model\Attachment;
use Corcel\Model\Option;

class Cms
{
    public static function logo_url()
    {
        $url = null;

        $siteIconId = Option::get('website_logo');

        if ($siteIconId) {
            $url = Attachment::find($siteIconId)->guid ?? '';
        }

        return $url;
    }

    public static function website_name()
    {
        $data = Option::get('website_name') ?? null;
        return $data;
    }


    public static function website_address()
    {
        $data = Option::get('website_address') ?? null;
        return $data;
    }

    public static function website_email()
    {
        $data = Option::get('website_email') ?? null;
        return $data;
    }

    public static function website_description()
    {
        $data = Option::get('website_description') ?? null;
        return $data;
    }

    public static function website_phone()
    {
        $data = Option::get('website_phone') ?? null;
        return $data;
    }
}
