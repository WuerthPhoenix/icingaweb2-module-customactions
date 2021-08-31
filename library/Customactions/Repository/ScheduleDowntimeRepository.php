<?php

namespace Icinga\Module\Customactions\Repository;

use Icinga\Module\Customactions\Repository\BaseActionLoggingRepository;

class ScheduleDowntimeRepository extends BaseActionLoggingRepository
{
    const MODULE_NAME = 'customactions';

    /**
     * ScheduleDowntimeRepository constructor.
     * The parent constructor is called passing the module name as parameter.
     * This will allow the repository to be reachable by hooks.
     */
    public function __construct()
    {
        parent::__construct(self::MODULE_NAME);
    }

}
