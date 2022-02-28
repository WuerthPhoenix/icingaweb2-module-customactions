<?php

namespace Icinga\Module\Customactions\Utils;

use Icinga\Application\Icinga;
use Icinga\Exception\ProgrammingError;

class DowntimePlannerUtil
{
    /**
     * Type host
     */
    const TYPE_HOST = "Host";

    /**
     * Type service
     */
    const TYPE_SERVICE = "Service";

    /**
     * Fixed downtime
     */
    const FIXED = 'fixed';

    /**
     * Flexible downtime
     */
    const FLEXIBLE = 'flexible';

    
    /**
     * DowntimeNoChildren child downtime
     */
    const DOWNTIME_NO_CHILDFREN = 'DowntimeNoChildren';

    /**
     * DowntimeTriggeredChildren child downtime
     */
    const DOWNTIME_TRIGGERED_CHILDREN = 'DowntimeTriggeredChildren';

    /**
     * DowntimeNonTriggeredChildren child downtime
     */
    const DOWNTIME_NON_TRIGGERED_CHILDREN = 'DowntimeNonTriggeredChildren';

    /**
     * Returns form values in case of edit-report
     * or autosubmit (when the customer is selected)
     * @return array
     * @throws ProgrammingError
     */
    public function getFormValue($key)
    {
        $values = self::FIXED;
        
        $requestParams = (Icinga::app())->getRequest()->getParams();

        console_log($requestParams);
        if (isset($requestParams[$key])) {
            $values = $requestParams[$key];
        }

        return $values;
    }
}

function console_log($data)
{
    echo '<script>';
    echo 'console.log(' . json_encode($data) . ')';
    echo '</script>';
}
