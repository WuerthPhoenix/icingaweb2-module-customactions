<?php

namespace Icinga\Module\Customactions\Utils;

use Icinga\Module\Neteye\Utils\BaseSessionUtil;

class SessionUtil extends BaseSessionUtil
{
    const COOKIE_NAME='customactions-downtime-results';

    /**
     * @param string $token
     * @return void
     * @throws \Exception
     */
    public static function storeDowntimeResults(array $results = []) : void
    {
        BaseSessionUtil::storeValue(self::COOKIE_NAME, $results);
    }

    /**
     * @return mixed
     */
    public static function retrieveDowntimeResults()
    {
        return BaseSessionUtil::retrieveValue(self::COOKIE_NAME);
    }
}