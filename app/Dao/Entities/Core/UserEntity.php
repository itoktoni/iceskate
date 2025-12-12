<?php

namespace App\Dao\Entities\Core;

use App\Facades\Model\RoleModel;

trait UserEntity
{
    /**
     * Method field_phone
     *
     * @return void
     */
    public static function field_phone()
    {
        return 'phone';
    }

    public function getFieldPhoneAttribute()
    {
        return $this->{self::field_phone()};
    }

    /**
     * Method field_email
     *
     * @return void
     */
    public static function field_email()
    {
        return 'email';
    }

    public function getFieldEmailAttribute()
    {
        return $this->{self::field_email()};
    }

    /**
     * Method field_username
     *
     * @return void
     */
    public static function field_username()
    {
        return 'username';
    }

    public function getFieldUsernameAttribute()
    {
        return $this->{self::field_username()};
    }

    public static function field_member()
    {
        return 'member';
    }

    public function getFieldemberAttribute()
    {
        return $this->{self::field_member()};
    }

    /**
     * Method field_level
     *
     * @return void
     */
    public static function field_level()
    {
        return 'level';
    }

    public function getFieldLevelAttribute()
    {
        return $this->{self::field_level()};
    }

    /**
     * Method field_active
     *
     * @return void
     */
    public static function field_active()
    {
        return 'active';
    }

    public function getFieldActiveAttribute()
    {
        return $this->{self::field_active()};
    }

    public static function field_category()
    {
        return 'category';
    }

    public function getFieldCategoryAttribute()
    {
        return $this->{self::field_category()};
    }


    /**
     * Method field_role
     *
     * @return void
     */
    public static function field_role()
    {
        return 'role';
    }

    public function getFieldRoleAttribute()
    {
        return $this->{self::field_role()};
    }

    public function getFieldRoleNameAttribute()
    {
        return $this->{RoleModel::field_name()};
    }
}
