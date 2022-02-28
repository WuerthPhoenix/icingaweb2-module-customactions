<?php

namespace Icinga\Module\Customactions\Web\Table;

use Icinga\Module\Neteye\Web\Table\BaseTable;
use Icinga\Module\Customactions\Repository\ApiErrorResultRepository;

class ApiErrorResultTable extends BaseTable
{
    public function __construct($models = [])
    {
        $repo = new ApiErrorResultRepository();
        $models = $repo->findAll();
        parent::__construct($models);
    }

    protected function getRowHref($model)
    {
        return '';
    }

    protected function renderStatusHeader()
    {
        return "Return Message";
    }

    protected function renderErrorHeader()
    {
        return "Return Code";
    }
}
