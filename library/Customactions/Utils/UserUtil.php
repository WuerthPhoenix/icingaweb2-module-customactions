<?php

namespace Icinga\Module\Customactions\Utils;

use Icinga\Application\Icinga;

class UserUtil
{
    /**
     * @return mixed
     * @throws \Icinga\Exception\ProgrammingError
     */
    public static function getUser()
    {
        return Icinga::app()->getRequest()->getUser()->getLocalUsername();
    }

    public static function getUserRestrictions()
    {
        return Icinga::app()->getRequest()->getUser()->getRestrictions('customactions/filter/categories');
    }

    public static function getUserRestrictionsForCategoryBox()
    {
        $result = '';
        $restrictions = self::getUserRestrictions();

        if (!empty($restrictions)) {
            $result = explode(',', $restrictions[0]);
        }
        return $result;
    }
}
