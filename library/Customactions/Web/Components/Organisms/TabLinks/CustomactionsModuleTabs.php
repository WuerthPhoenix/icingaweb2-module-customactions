<?php

namespace Icinga\Module\Customactions\Web\Components\Organisms\TabLinks;

use Icinga\Module\Neteye\Web\Components\Organisms\TabLinks\BaseModuleTabs;
use Icinga\Util\Translator;

class CustomactionsModuleTabs extends BaseModuleTabs
{
    public function getControllersAndDisplayNames(): array
    {
        return [
            'filter' => Translator::translate('Filter', 'customactions'),
            'category' => Translator::translate('Category', 'customactions'),
        ];
    }
}
