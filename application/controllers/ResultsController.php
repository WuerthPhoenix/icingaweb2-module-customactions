<?php

namespace Icinga\Module\Customactions\Controllers;

use Icinga\Module\Customactions\Repository\ApiErrorResultRepository;
use Icinga\Module\Neteye\BaseController;
use Icinga\Module\Customactions\Utils\PermissionUtil;
use Icinga\Module\Customactions\Web\Table\ApiErrorResultTable;
use Icinga\Util\Translator;
use Icinga\Module\Customactions\Web\Components\Organisms\QuickLinks\ReturnItemBar;
use Icinga\Module\Neteye\Web\Components\Organisms\TabLinks\SingleTab;
use ipl\Html\Html;

class ResultsController extends BaseController
{
    protected $request;

    public function indexAction()
    {
        PermissionUtil::isAllowedForAdmin();
        $this->setAutorefreshInterval(self::DEFAULT_AUTOREFRESH_INTERVAL);

        $this->setViewTitle(Translator::translate('Results', 'customactions'));
        
        $this->request = $this->getRequest();

        $this->prepareAction('index');

        $this->controls()->add(new ReturnItemBar($this->request, array('customactions/downtimes', 'monitoring/list/downtimes', 'auditlog/activities')));
        
        $this->content()->add(Html::tag('h1', [
            'class' => 'information'
        ], $this->translate('Result')));

        $repo = new ApiErrorResultRepository();
        $models = $repo->findAll();

        if (empty($models)) {
            $this->content()->add(Html::tag('p', [
                'class' => 'information'
            ], Translator::translate('Last downtime(s) successfully planned', "customactions")));
        } else {
            $this->content()->add(new ApiErrorResultTable());
        }
    }

    private function prepareAction($actionName)
    {
        $controllerName = $this->request->getControllerName();

        $addTab = new SingleTab(
            $this->getModuleName(),
            $controllerName,
            Translator::translate(ucfirst($controllerName), 'customactions'),
            $actionName
        );

        $addTab->activate($actionName . $controllerName);
        $this->controls()->add($addTab);
    }
}