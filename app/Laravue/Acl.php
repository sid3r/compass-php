<?php
/**
 * File Acl.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */
namespace App\Laravue;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class Acl
 *
 * @package App\Laravue
 */
final class Acl
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_VENDOR = 'vendor';
    // const ROLE_TRANSPORTER = 'transporter';
    // const ROLE_VISITOR = 'visitor';

    const PERMISSION_VIEW_MENU_CONTACTS = 'view menu contacts';
    const PERMISSION_VIEW_MENU_COMPANIES = 'view menu companies';
    const PERMISSION_VIEW_MENU_DOCUMENTS= 'view menu documents';
    const PERMISSION_VIEW_MENU_PRODUCTS = 'view menu products';
    const PERMISSION_VIEW_MENU_PURCHASES = 'view menu purchases';
    const PERMISSION_VIEW_MENU_SALES = 'view menu sales';
    const PERMISSION_VIEW_MENU_STOCKS = 'view menu stocks';
    const PERMISSION_VIEW_MENU_STOREHOUSES = 'view menu storehouses';
    const PERMISSION_VIEW_MENU_ADMINISTRATION = 'view menu administration';

    const PERMISSION_MANAGE_USERS = 'manage users';
    const PERMISSION_MANAGE_PERMISSIONS = 'manage permissions';

    /**
     * @param array $exclusives Exclude some permissions from the list
     * @return array
     */
    public static function permissions(array $exclusives = []): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) use ($exclusives) {
                return !in_array($value, $exclusives) && Str::startsWith($key, 'PERMISSION_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    public static function menuPermissions(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'PERMISSION_VIEW_MENU_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    /**
     * @return array
     */
    public static function roles(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $roles =  Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'ROLE_');
            });

            return array_values($roles);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }
}
