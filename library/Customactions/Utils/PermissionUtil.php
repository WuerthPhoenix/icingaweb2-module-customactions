<?php

namespace Icinga\Module\Customactions\Utils;

use Icinga\Module\Neteye\Utils\UserPermissionUtil;
use Icinga\Authentication\Auth;
use Icinga\Module\Geomap\Repository\MapRepository;
use Icinga\Security\SecurityException;

class PermissionUtil extends UserPermissionUtil
{
    const FULL_MODULE_ACCESS_PERMISSION = 'customactions/*';
    const GENERAL_MODULE_ACCESS_PERMISSION = 'module/customactions';

    public static function isAllowedForAdmin()
    {
        return (new self())->checkPermissions(Auth::getInstance(), [self::FULL_MODULE_ACCESS_PERMISSION]);
    }

    /**
     * @return bool
     * @throws ProgrammingError
     */
    public static function isCustomactionsAdmin(): bool
    {
        $userPermissions = self::getUserPermissions();
        return (isset($userPermissions['*']) || isset($userPermissions[self::FULL_MODULE_ACCESS_PERMISSION]));
    }

    /**
     * @return bool
     * @throws ProgrammingError
     */
    public static function hasCustomactionsAccess(): bool
    {
        $userPermissions = self::getUserPermissions();
        return (self::isCustomactionsAdmin() || isset($userPermissions[self::GENERAL_MODULE_ACCESS_PERMISSION]));
    }
}
