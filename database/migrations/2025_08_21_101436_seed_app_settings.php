<?php

use App\Dao\Models\Core\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // system group
        DB::table('system_group')->delete();
        DB::table('system_group')->insert([
            0 => [
                'system_group_code' => 'aplikasi',
                'system_group_name' => 'Aplikasi',
                'system_group_sort' => 2,
                'system_group_enable' => 1,
                'system_group_url' => null,
                'system_group_icon' => 'window-sidebar',
                'system_group_description' => null,
            ],
            1 => [
                'system_group_code' => 'laporan',
                'system_group_name' => 'Laporan',
                'system_group_sort' => 1,
                'system_group_enable' => 1,
                'system_group_url' => null,
                'system_group_icon' => 'printer',
                'system_group_description' => null,
            ],
            2 => [
                'system_group_code' => 'master',
                'system_group_name' => 'Master',
                'system_group_sort' => 7,
                'system_group_enable' => 1,
                'system_group_url' => null,
                'system_group_icon' => 'database',
                'system_group_description' => null,
            ],
            3 => [
                'system_group_code' => 'setting',
                'system_group_name' => 'System',
                'system_group_sort' => -1,
                'system_group_enable' => 1,
                'system_group_url' => null,
                'system_group_icon' => 'wrench-adjustable-circle',
                'system_group_description' => null,
            ],
        ]);

        // system menu
        DB::table('system_menu')->delete();
        DB::table('system_menu')->insert([
            [
                'system_menu_code' => 'roles',
                'system_menu_name' => 'Roles',
                'system_menu_url' => 'roles',
                'system_menu_controller' => 'App\\Http\\Controllers\\Core\\RolesController',
                'system_menu_action' => 'roles.getTable',
                'system_menu_type' => 1,
                'system_menu_sort' => 9,
                'system_menu_description' => null,
                'system_menu_enable' => null,
                'system_menu_can_delete' => 1,
            ],
            [
                'system_menu_code' => 'user',
                'system_menu_name' => 'User',
                'system_menu_url' => 'user',
                'system_menu_controller' => 'App\\Http\\Controllers\\Core\\UserController',
                'system_menu_action' => 'user.getTable',
                'system_menu_type' => 1,
                'system_menu_sort' => null,
                'system_menu_description' => '',
                'system_menu_enable' => null,
                'system_menu_can_delete' => 1,
            ],
            [
                'system_menu_code' => 'master',
                'system_menu_name' => 'Master',
                'system_menu_url' => 'master',
                'system_menu_controller' => null,
                'system_menu_action' => null,
                'system_menu_type' => 2,
                'system_menu_sort' => null,
                'system_menu_description' => null,
                'system_menu_enable' => null,
                'system_menu_can_delete' => 1,
            ],
            [
                'system_menu_code' => 'setting',
                'system_menu_name' => 'Setting Website',
                'system_menu_url' => 'pengaturan',
                'system_menu_controller' => 'App\\Http\\Controllers\\Core\\SettingController',
                'system_menu_action' => 'setting.getCreate',
                'system_menu_type' => 1,
                'system_menu_sort' => null,
                'system_menu_description' => '',
                'system_menu_enable' => null,
                'system_menu_can_delete' => 1,
            ],
            [
                'system_menu_code' => 'groups',
                'system_menu_name' => 'Group',
                'system_menu_url' => 'groups',
                'system_menu_controller' => 'App\\Http\\Controllers\\Core\\GroupsController',
                'system_menu_action' => 'groups.getTable',
                'system_menu_type' => 1,
                'system_menu_sort' => 10,
                'system_menu_description' => null,
                'system_menu_enable' => null,
                'system_menu_can_delete' => 1,
            ],
            [
                'system_menu_code' => 'menu',
                'system_menu_name' => 'Menu',
                'system_menu_url' => 'menu',
                'system_menu_controller' => 'App\\Http\\Controllers\\Core\\MenuController',
                'system_menu_action' => 'menu.getTable',
                'system_menu_type' => 1,
                'system_menu_sort' => 8,
                'system_menu_description' => '',
                'system_menu_enable' => null,
                'system_menu_can_delete' => 1,
            ],
            [
                'system_menu_code' => 'link',
                'system_menu_name' => 'Link',
                'system_menu_url' => 'link',
                'system_menu_controller' => 'App\\Http\\Controllers\\Core\\LinkController',
                'system_menu_action' => 'link.getTable',
                'system_menu_type' => 1,
                'system_menu_sort' => 7,
                'system_menu_description' => null,
                'system_menu_enable' => null,
                'system_menu_can_delete' => 1,
            ],
            [
                'system_menu_code' => 'permission',
                'system_menu_name' => 'Permission',
                'system_menu_url' => 'permission',
                'system_menu_controller' => 'App\\Http\\Controllers\\Core\\PermissionController',
                'system_menu_action' => 'permission.getTable',
                'system_menu_type' => 1,
                'system_menu_sort' => null,
                'system_menu_description' => null,
                'system_menu_enable' => null,
                'system_menu_can_delete' => 1,
            ],
            [
                'system_menu_code' => 'report_user',
                'system_menu_name' => 'Report User',
                'system_menu_url' => 'report_user',
                'system_menu_controller' => 'App\Http\Controllers\ReportUserController',
                'system_menu_action' => 'report_user.getCreate',
                'system_menu_type' => 1,
                'system_menu_sort' => null,
                'system_menu_description' => null,
                'system_menu_enable' => null,
                'system_menu_can_delete' => 1,
            ],
        ]);

        // system role
        DB::table('system_role')->delete();
        DB::table('system_role')->insert([
            0 => [
                'system_role_code' => 'admin',
                'system_role_name' => 'Admin',
                'system_role_description' => 'ini buat seluruh system untuk mengatur',
                'system_role_level' => 100,
            ],
            1 => [
                'system_role_code' => 'user',
                'system_role_name' => 'User',
                'system_role_description' => 'pengguna',
                'system_role_level' => 1,
            ],
        ]);

        // system group connection menu
        DB::table('system_group_connection_menu')->delete();
        DB::table('system_group_connection_menu')->insert([
            0 => [
                'system_group_code' => 'setting',
                'system_menu_code' => 'link',
            ],
            1 => [
                'system_group_code' => 'setting',
                'system_menu_code' => 'menu',
            ],
            2 => [
                'system_group_code' => 'setting',
                'system_menu_code' => 'permission',
            ],
            3 => [
                'system_group_code' => 'setting',
                'system_menu_code' => 'roles',
            ],
            4 => [
                'system_group_code' => 'setting',
                'system_menu_code' => 'setting',
            ],
            5 => [
                'system_group_code' => 'setting',
                'system_menu_code' => 'user',
            ],
            6 => [
                'system_group_code' => 'setting',
                'system_menu_code' => 'groups',
            ],
            7 => [
                'system_group_code' => 'laporan',
                'system_menu_code' => 'report_user',
            ],
        ]);

        // system group connection role
        DB::table('system_group_connection_role')->delete();
        DB::table('system_group_connection_role')->insert([
            0 => [
                'system_role_code' => 'admin',
                'system_group_code' => 'aplikasi',
            ],
            1 => [
                'system_role_code' => 'admin',
                'system_group_code' => 'laporan',
            ],
            2 => [
                'system_role_code' => 'admin',
                'system_group_code' => 'setting',
            ],
        ]);

        // user
        DB::table('users')->delete();
        DB::table('users')->insert([
            'id' => 0,
            'name' => 'Admin',
            'username' => 'admin',
            'phone' => null,
            'email' => 'admin@gmail.com',
            'email_verified_at' => '2022-12-13 18:51:38',
            'password' => Hash::make('admin'), // password
            'role' => 'admin',
            'level' => 100,
            'active' => 1,
            'remember_token' => null,
            'created_at' => null,
            'updated_at' => '2023-02-25 10:54:19',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_group');
        Schema::dropIfExists('system_menu');
        Schema::dropIfExists('system_role');
        Schema::dropIfExists('system_group_connection_menu');
        Schema::dropIfExists('system_group_connection_role');
        Schema::dropIfExists('users');
    }
};
