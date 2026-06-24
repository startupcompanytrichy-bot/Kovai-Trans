<?php

/**
 * Permission Helper Functions
 *
 * Usage in Blade:
 * @if(userCan('view_trips'))
 *     <li>Show trips menu</li>
 * @endif
 */

use App\Models\Login;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;

if (!function_exists('currentLogin')) {
    /**
     * Get the currently logged in user from session
     * Returns Login model instance or null
     */
    function currentLogin()
    {
        $loginId = session('loginId');
        if (!$loginId) {
            return null;
        }
        return Login::find($loginId);
    }
}

if (!function_exists('isLoggedIn')) {
    /**
     * Check if a user is logged in via session
     */
    function isLoggedIn()
    {
        return session('loginId') !== null && currentLogin() !== null;
    }
}

if (!function_exists('userCan')) {
    /**
     * Check if authenticated user has a permission
     */
    function userCan($permission)
    {
        // First check if user is logged in via session
        if (!isLoggedIn()) {
            return false;
        }

        // Check if user has the permission directly
        $login = currentLogin();
        if (!$login) {
            return false;
        }

        // Find User model via relationship
        $user = $login->user;
        if ($user) {
            // Auto-sync role from login if not yet in user_roles
            if ($user->roles()->count() === 0 && $login->role) {
                $role = Role::where('name', $login->role)->first();
                if ($role) {
                    $user->roles()->sync([$role->id]);
                }
            }
            return $user->hasPermission($permission);
        }

        return false;
    }
}

if (!function_exists('userCanAny')) {
    /**
     * Check if authenticated user has any of the given permissions
     */
    function userCanAny($permissions)
    {
        if (!isLoggedIn()) {
            return false;
        }

        foreach ((array) $permissions as $permission) {
            if (userCan($permission)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('userCanAll')) {
    /**
     * Check if authenticated user has all of the given permissions
     */
    function userCanAll($permissions)
    {
        if (!isLoggedIn()) {
            return false;
        }

        foreach ((array) $permissions as $permission) {
            if (!userCan($permission)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('userHasRole')) {
    /**
     * Check if authenticated user has a role
     */
    function userHasRole($role)
    {
        if (!isLoggedIn()) {
            return false;
        }

        $login = currentLogin();
        if (!$login) {
            return false;
        }

        // Check role from Login model's role field
        return strtolower($login->role) === strtolower($role);
    }
}

if (!function_exists('showAllMenu')) {
    /**
     * Check if the current user should see all menu items
     * Returns true for Super Admin users (they can see all items)
     * Returns false for other users (permission-based visibility)
     */
    function showAllMenu()
    {
        if (!isLoggedIn()) {
            return false;
        }

        $login = currentLogin();
        if (!$login) {
            return false;
        }

        // Only Super Admin can see all menu items
        return in_array(strtolower($login->role), ['super_admin', 'super admin']);
    }
}

if (!function_exists('userCanSeeMenu')) {
    /**
     * Check if the current user has a specific menu assigned
     */
    function userCanSeeMenu($menuName)
    {
        if (!isLoggedIn()) {
            return false;
        }

        $login = currentLogin();
        if (!$login) {
            return false;
        }

        $user = $login->user;
        if (!$user) {
            return false;
        }

        return $user->menus()->where('name', $menuName)->exists();
    }
}

if (!function_exists('userCanSeeAnyMenu')) {
    /**
     * Check if the current user has any of the given menus assigned
     */
    function userCanSeeAnyMenu($menuNames)
    {
        if (!isLoggedIn()) {
            return false;
        }

        $login = currentLogin();
        if (!$login) {
            return false;
        }

        $user = $login->user;
        if (!$user) {
            return false;
        }

        return $user->menus()->whereIn('name', (array) $menuNames)->exists();
    }
}

if (!function_exists('userCanView')) {
    /**
     * Combined check: user has permission AND menu is assigned
     * Admin/super admin bypasses the menu check
     */
    function userCanView($permission, $menuName = null)
    {
        if (!isLoggedIn()) {
            return false;
        }

        if (showAllMenu()) {
            return true;
        }

        if (!userCan($permission)) {
            return false;
        }

        if ($menuName && !userCanSeeMenu($menuName)) {
            return false;
        }

        return true;
    }
}
