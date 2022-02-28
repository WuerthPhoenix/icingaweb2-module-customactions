<?php

$this->provideRestriction(
    'customactions/filter/categories',
    $this->translate(
        'Limit access to the filters of categories defined by this regex.'
    )
);


$menuSection = $this->menuSection(N_('Custom Actions'), array(
    'url'      => '/neteye/customactions/downtimes',
    'icon'     => 'sliders',
));

$menuSection->add(N_('DowntimePlanner'))
    ->setUrl('customactions/downtimes')
    ->setPriority(10);

$auth = \Icinga\Authentication\Auth::getInstance();
$permission = (new \Icinga\Module\Customactions\Utils\PermissionUtil($auth));
if ($permission->isCustomactionsAdmin()) {
    $menuSection->add(N_('Configurator'))
        ->setUrl('customactions/filter')
        ->setPriority(30);
}