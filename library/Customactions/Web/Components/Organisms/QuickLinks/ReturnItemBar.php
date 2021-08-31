<?php

namespace Icinga\Module\Customactions\Web\Components\Organisms\QuickLinks;

use Icinga\Module\Neteye\Html\Components\Organisms\QuickLinks\ActionBarIpl;

class ReturnItemBar extends ActionBarIpl
{

    public function __construct($request, $link)
    {
        $this->setUp($request, $link);
    }

    protected function setup($request, $link)
    {
        $actionLinks = array(
            array(
                'title' => 'Back to home',
                'icon' => 'home',
                'link' => $link[0],
                'class' => 'color-green',
                'target' => "_main"
            ),
            array(
                'title' => 'Show active downtimes',
                'icon' => 'plug',
                'link' => $link[1],
                'class' => 'color-green',
                'target' => "_next",
                'urlParams' => [
                    'downtime_is_in_effect' => 1,
                ]
            ),
            array(
                'title' => 'Show auditlog',
                'icon' => 'users',
                'link' => $link[2],
                'class' => 'color-green',
                'target' => "_main",
                'urlParams' => [
                    'user' => 0,
                    'module_name' => "Customactions",
                    'change_time' => ""    
                ]
            ),
        );

        $this->create($actionLinks, $request);
    }
}