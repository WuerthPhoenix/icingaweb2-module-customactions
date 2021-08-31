<?php

namespace Icinga\Module\Customactions\Repository;

use Icinga\Module\Customactions\Repository\DatabaselessRepository;

class ApiErrorResultRepository extends DatabaselessRepository
{
    const MODULE_NAME = 'customactions';

    /**
     * CategoryRepository constructor.
     * The parent constructor is called passing the module name as parameter.
     * This will allow the repository to be reachable by hooks.
     */
    public function __construct()
    {
        parent::__construct(self::MODULE_NAME);
    }

}
