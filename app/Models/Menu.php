<?php

namespace App\Models;

use Corcel\Model\Menu as Model;
use Corcel\Model\MenuItem;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    public static function getMenuBySlug(string $slug)
    {
        $menu = self::slug($slug)->first();

        if ($menu) {
            return $menu->items;
        }

        return null;
    }

    public static function showNavigation()
    {
        $menuItems = Menu::getMenuBySlug('top');

         $branch = [];

        foreach ($menuItems as $item) {
            if ($item->post_parent == $parentId) {
                $children = $this->buildMenuHierarchy($menuItems, $item->ID);

                // Ensure we use the correct title/url accessors
                $item->display_title = $item->instance()->post_title ?? $item->title;
                $item->display_url = $item->instance()->guid ?? $item->url;

                if ($children) {
                    $item->children_items = $children;
                }
                $branch[] = $item;
            }
        }

    return $branch;
    }

    public static function checkMenuData()
    {

        // Find the content relation ID (most likely what you want)
$menuItems = DB::table('cms_posts AS child')
    ->select([
        'child.post_title AS menu',
        DB::raw("COALESCE(parent.post_title, '0') AS parent")
    ])
    ->leftJoin('cms_posts AS parent', 'child.post_parent', '=', 'parent.ID')
    ->where('child.post_type', 'nav_menu_item')
    ->orderBy('child.menu_order', 'asc')
    ->get();

return $menuItems;

return $relationId;
        // Use the MenuItem model which points to the 'posts' table
        $menuItems = MenuItem::select('ID', 'post_title', 'post_type', 'post_parent', 'guid')
            ->where('post_type', 'nav_menu_item')
            ->get();

        // Dump the data to see the raw results from the correct table
        dd($menuItems);
    }
}