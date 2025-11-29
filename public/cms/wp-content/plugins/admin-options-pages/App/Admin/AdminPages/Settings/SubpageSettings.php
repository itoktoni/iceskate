<?php

namespace AOP\App\Admin\AdminPages\Settings;

use AOP\App\Plugin;

class SubpageSettings
{
    const MENU_TITLE       = 'Settings';
    const PAGE_TITLE       = 'Settings';
    const SLUG             = Plugin::_NAME . '_settings';
    const HIDE_EDIT_BUTTON = Plugin::PREFIX_ . 'setting_editpage_hidden';

    public static function isSettingsPage()
    {
        return $_REQUEST['page'] === self::SLUG;
    }
}
