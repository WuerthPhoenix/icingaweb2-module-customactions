<?php

namespace Icinga\Module\Customactions\Controllers;

use Icinga\Module\Neteye\BaseController;
use Icinga\Module\Neteye\Web\Components\Organisms\TabLinks\SingleTab;
use Icinga\Module\Customactions\Utils\PermissionUtil;
use Icinga\Module\Customactions\Web\Form\DowntimesForm;
use Icinga\Module\Customactions\Web\Form\ScheduleDowntimeForm;
use Icinga\Util\Translator;
use Icinga\Application\Icinga;

class DowntimesController extends BaseController
{


    /** @var Response */
    protected $icingaResponse;

    /**
     * @throws \Icinga\Exception\ProgrammingError
     * @throws \ReflectionException
     */
    public function indexAction()
    {
        $this->prepareAction('index');

        $this->setViewTitle(Translator::translate('Downtimes', 'customactions'));

        $this->content()->add(new DowntimesForm());
    }

    private function prepareAction($actionName)
    {
        $request = $this->getRequest();
        $controllerName = $request->getControllerName();

        $addTab = new SingleTab(
            $this->getModuleName(),
            $controllerName,
            Translator::translate($actionName == 'index' ? ucfirst($controllerName) : ucfirst($actionName), 'customactions'),
            $actionName
        );
        $addTab->activate($actionName . $controllerName);
        $this->controls()->add($addTab);
    }
}