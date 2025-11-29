<?php

namespace AOP\App\Admin\AdminPages;

use AOP\App\Plugin;

class SubpageMaster
{
    const MENU_TITLE  = 'All Options Pages';
    const PAGE_TITLE  = 'Options Pages';
    const PAGE_BUTTON = 'Add new';
    const SLUG        = Plugin::_NAME . '_master';

    public function __construct()
    {
        add_action('wp_loaded', [$this, 'onSubmitBulkAction']);
    }

    public function onSubmitBulkAction()
    {
        if (!isset($_REQUEST['page'])) {
            return;
        }

        if (!empty($_REQUEST['_wp_http_referer']) && $_REQUEST['page'] === $this->slug) {
            wp_redirect(remove_query_arg(['_wp_http_referer'], wp_unslash($_SERVER['REQUEST_URI'])));
            exit;
        }
    }

    public static function url()
    {
        return admin_url('admin.php?page=' . static::SLUG);
    }

    public static function isCurrentPage()
    {
        if (!isset($_REQUEST['page'])) {
            return false;
        }

        if ($_REQUEST['page'] !== static::SLUG) {
            return false;
        }

        return true;
    }
}
