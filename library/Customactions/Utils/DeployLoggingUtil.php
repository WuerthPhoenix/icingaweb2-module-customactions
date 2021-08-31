<?php

namespace Icinga\Module\Customactions\Utils;

use Icinga\Module\Auditlog\API\Logger;
use Icinga\Module\Neteye\Utils\BaseLoggingUtil;

class DeployLoggingUtil extends BaseLoggingUtil
{
    public static function sendDeployAuditLog($module, $objectType, $objectName, $url, $newValues, $user, $message = '')
    {
        if (!self::checkForAuditlogAvailability()) {
            return;
        }

        Logger::deploy(
            $module,
            $objectType,
            $objectName,
            $url,
            $newValues,
            $user,
            $message
        );
    }
    
}