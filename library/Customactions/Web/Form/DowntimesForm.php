<?php

namespace Icinga\Module\Customactions\Web\Form;

use DateTime;
use Icinga\Module\Customactions\Repository\FilterRepository;
use Icinga\Module\Customactions\Repository\CategoryRepository;
use Icinga\Module\Customactions\Repository\ScheduleDowntimeRepository;
use Icinga\Module\Customactions\RestApi\CustomRestApiClient;
use Icinga\Module\Customactions\Utils\CustomactionsConfigUtil;
use Icinga\Module\Customactions\Utils\DowntimePlannerUtil;
use Icinga\Module\Customactions\Utils\SessionUtil;
use Icinga\Util\Translator;
use Icinga\Web\Notification;
use ipl\Html\Html;
use Icinga\Module\Customactions\Web\Form\CustomactionsForm;
use Icinga\Module\Neteye\Utils\BaseFormUtil;
use Icinga\Web\Url;

class DowntimesForm extends CustomactionsForm
{
    protected $filterRepository;
    protected $categoryRepository;
    protected $urlHelper;
    protected $category;
    protected $filterObjects;




    // Schedule
    // START
    const ICINGAWEB2_HOST = 'httpd.neteyelocal';
    const EXECUTE_COMMAND_ENDPOINT = 'v1/actions/schedule-downtime';
    const LAST_EXECUTION_RESULT_TAB = '/neteye/cmdorchestrator/lastexecutionresult/index';

    // protected $filterRepository;
    protected $scheduleDowntimeRepository;
    protected $actionName;
    // protected $urlHelper;

    protected $filters;
    protected $downtimePlannerUtil;
    protected $type;
    protected $api;
    // END

    public function __construct()
    {
        $this->filterRepository = new FilterRepository();
        $this->categoryRepository = new CategoryRepository();
        $this->urlHelper = new \Zend_Controller_Action_Helper_Url();
        $this->scheduleDowntimeRepository = new ScheduleDowntimeRepository();
        $this->downtimePlannerUtil = new DowntimePlannerUtil();

        // $this->type = $this->downtimePlannerUtil->getFormValue("type");
        // console_log($this->type);

        $this->type = $this->getIcingaRequest()->getParam("type"); //$this->downtimePlannerUtil->getFormValue("type");
        if (empty($this->type))
            $this->type = DowntimePlannerUtil::FIXED;

        $this->addElementLoader('Icinga\Module\Neteye\Web\Form\Element');
        $this->addElementLoader('Icinga\Module\Customactions\Web\Form\Element');

        parent::__construct();
    }

    public function assemble()
    {
        $categoryId = $this->getIcingaRequest()->getParam("category");
        if (isset($categoryId)) {
            $this->category = $this->categoryRepository->findCategoryByIdWithRestriction($categoryId);
        }
        $this->addCategoryBox();

        if (isset($this->category) && !empty($this->category)) {
            $this->addDescription();

            $this->addScheduler();

            $this->addFilters();

            $this->addSubmitButton(
                Translator::translate('Store', $this->getIcingaRequest()->getModuleName())
            );
        } else {
            $this->add(Html::tag(
                'div',
                ['class' => 'information'],
                Translator::translate(
                    'No category was selected yet',
                    'customactions'
                )
            ));
        }
    }

    protected function addCategoryBox()
    {
        $categoryObjects = $this->categoryRepository->findCategoriesWithRestriction();

        $categorySelectBox = $this->createElement(
            'baseSelectElement',
            'category',
            [
                'label' => Html::tag('h2', [
                    'class' => 'information'
                ], Translator::translate('Category', 'customactions')),
                'options' => BaseFormUtil::convertModelToOptions($categoryObjects, 'name'),
                'class' => 'autosubmit',
            ]
        )->setValue(isset($this->category) && !empty($this->category) ? $this->category->getId() : "")
            ->disableOption("");

        $this->addElement($categorySelectBox);
    }

    protected function addDescription()
    {
        $this->add(
            Html::tag('div', ['class' => 'container'])
                ->add(
                    Html::tag('h1', [
                        'class' => 'information icon-info'
                    ], Translator::translate('Description', 'customactions'))
                )->add(
                    Html::tag('p', [
                        'class' => 'information'
                    ], ($this->category->getDescription() ?: "No description available"))
                )
        );
    }


    protected function addFilters()
    {
        $this->filterObjects = $this->filterRepository->getFilterListByCategory($this->getValue("category"), "name");

        $this->add(
            Html::tag('h1', [
                'class' => 'information icon-sliders'
            ], Translator::translate('Filters', 'customactions'))
        );

        foreach ($this->filterObjects as $key => $value) {
            $attributes = [
                'label' => $value
            ];

            $elementDescription = $this->filterRepository->findById($key)->getValue("description");

            if (!empty($elementDescription)) {
                $attributes['description'] = $elementDescription;
            }

            $mainPropertiesElements[] =
                $this->createElementInColumns('baseCheckboxElement', 'filter' . $key, $attributes);
        }
        if (isset($mainPropertiesElements))
            $this->addElements($mainPropertiesElements);
        else {
            $this->add(Html::tag(
                'div',
                ['class' => 'control-group no-permission-message'],
                Translator::translate(
                    'This category does not contain any filters',
                    'customactions'
                )
            ));
        }
    }

    public function hasBeenSubmitted()
    {
        if ($this->hasBeenSent() && !parent::hasBeenSubmitted()) {
            $url = Url::fromPath("customactions/downtimes");
            $url->setParam("category", $this->getIcingaRequest()->getPost("category"));
            $type = $this->getIcingaRequest()->getPost("type");
            if (empty($type))
                $type = DowntimePlannerUtil::FIXED;
            $url->setParam("type", $type);
            $this->getIcingaResponse()->redirectAndExit($url);
        } else {
            return parent::hasBeenSubmitted();
        }
    }

    // public function onSuccess()
    // {
    //     $params = $this->prepareFilterParamsForUrl();

    //     if (empty($params) && $this->hasBeenSent() && parent::hasBeenSubmitted()) {
    //         Notification::error(Translator::translate('Cannot go to planning form: No filter selected', 'monitoring'));
    //     } else {
    //         $this->redirectOnSuccess($params);
    //     }
    // }

    protected function setSelectedFilters()
    {
        $values = $this->getValues();

        $values = array_filter(
            $values,
            function ($value, $key) {
                return $value == '1' && strpos($key, 'filter') !== false;
            },
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($values as $key => $value) {
            $ids[] = str_replace('filter', '', $key);
        }

        $this->filters = $this->filterRepository->getFiltersByCondition(["id" => $ids]);
    }

    // protected function redirectOnSuccess($filters)
    // {
    //     $url = $this->urlHelper->direct("index") . '?category=' . $this->getIcingaRequest()->getParam("category") . '#!' . $this->urlHelper->direct("add");

    //     if (!empty($filters)) {
    //         $url .= '?' . http_build_query($filters);
    //     }

    //     $this->getIcingaResponse()->redirectAndExit($url);
    // }


    // Scheduler
    // Start
    public function addScheduler()
    {
        $mainPropertiesSet = $this->createLegend(
            Translator::translate('Main properties', 'customactions')
        )->addWrapper(Html::tag('fieldset'));
        $this->add($mainPropertiesSet);

        $mainPropertiesElements = [
            $this->createElement(
                'textarea',
                'comment',
                [
                    'label' => Translator::translate('Comment', 'monitoring'),
                    'class' => 'icon-textarea',
                    'required' => true
                ]
            ),
            $this->createElement(
                'baseSelectElement',
                'type',
                [
                    'required' => true,
                    'label' => Translator::translate('Type', 'monitoring'),
                    'options' => $this->filterRepository->getTypeOptions(),
                    'class' => 'autosubmit',
                ]
            )->setValue($this->type),
            $this->createElement(
                'baseLocalDateTimeElement',
                'start',
                [
                    'label' => Translator::translate('Start Time', 'monitoring'),
                    'required' => true
                ]
            ),
            $this->createElement(
                'baseLocalDateTimeElement',
                'end',
                [
                    'label' => Translator::translate('End Time', 'monitoring'),
                    'required' => true
                ]
            )


        ];

        $sharedPropertiesElements = [];
        $addSharedPropertiesElements = true; //$this->filtersContainHost();

        if ($addSharedPropertiesElements) {
            $sharedPropertiesElements[] =
                $this->createElement(
                    'baseCheckboxElement',
                    'all_services',
                    [
                        'label' => Translator::translate('All Services', 'monitoring'),
                        'checked' => $this->getIcingaRequest()->getPost("all_services")
                    ]
                );

            $sharedPropertiesElements[] =
                $this->createElement(
                    'baseSelectElement',
                    'child_hosts',
                    [
                        'label' => Translator::translate('Child Hosts', 'monitoring'),
                        'options' => $this->filterRepository->getChildHostsOptions(),
                    ]
                );
        }

        $flexiblePropertiesElements = [];

        if ($this->type === DowntimePlannerUtil::FIXED) {
            $mainPropertiesElements = array_merge($mainPropertiesElements, $sharedPropertiesElements);
            $this->addElements($mainPropertiesElements);
        } else {
            $this->addElements($mainPropertiesElements);

            $flexiblePropertiesSet = $mainPropertiesSet = $this->createLegend(
                Translator::translate('Flexible Duration', 'monitoring')
            )->addWrapper(Html::tag('fieldset'));
            $this->add($flexiblePropertiesSet);

            $flexiblePropertiesElements = [
                $this->createElement(
                    'number',
                    'hours',
                    [
                        'label' => Translator::translate('Hours', 'monitoring'),
                        'required' => true,
                        'value' => 2,
                        'min' => -1
                    ]
                ),
                $this->createElement(
                    'number',
                    'minutes',
                    [
                        'label' => Translator::translate('Minutes', 'monitoring'),
                        'required' => true,
                        'value'     => 0,
                        'min'       => -1
                    ]
                )
            ];

            if ($addSharedPropertiesElements) {
                $flexiblePropertiesElements = array_merge($flexiblePropertiesElements, $sharedPropertiesElements);
            }
            $this->addElements($flexiblePropertiesElements);
        }
    }

    // public function hasBeenSubmitted()
    // {
    //     if ($this->hasBeenSent() && $this->shouldBeDeleted()) {
    //         $id = $this->getIcingaRequest()->getParam('id');
    //         $this->redirectForDelete($id);
    //     } else {
    //         return parent::hasBeenSubmitted();
    //     }
    // }

    public function onSuccess()
    {
        $this->setSelectedFilters();
        $end = $this->getValue('end')->getTimestamp();
        if ($end <= $this->getValue('start')->getTimestamp()) {
            Notification::error(Translator::translate('The end time must be greater than the start time', 'monitoring'));
            return false;
        }

        $now = new DateTime();
        if ($end <= $now->getTimestamp()) {
            Notification::error(Translator::translate('A downtime must not be in the past', 'monitoring'));
            return false;
        }

        $errors = [];
        $id = 0;

        foreach ($this->filters as $name => $filter) {
            foreach ($filter as $type => $filter) {
                if (isset($filter)) {
                    $requestBody = $this->buildTypedRequestBody($type, $filter);
                    try {

                        $response = $this->callCommandExecution($requestBody);
                        $result = array($id++, $name, $type, $response->body, $response->status, $response->error);
                        $modelObject = $this->scheduleDowntimeRepository->convertToSingleModelObject($result);

                        $this->scheduleDowntimeRepository->add($modelObject);

                        if ($modelObject->getError() >= 400)
                            $errors[] = $result;
                    } catch (\Exception $e) {
                        Notification::error($e->getMessage());
                        return;
                    }
                }
            }
        }
        if (empty($errors)) {
            Notification::success(Translator::translate('All downtimes planned successfully', "customaction"));
        } else {
            Notification::error(Translator::translate('Error while planning downtimes', "customaction"));
        }
        SessionUtil::storeDowntimeResults($errors);
        $this->redirectOnSuccess();
    }

    protected function buildTypedRequestBody($objectType, $filter): array
    {
        $isFixed = $this->getValue("type") == DowntimePlannerUtil::FIXED;

        $requestBody = [
            'author' => $this->getIcingaRequest()->getUser()->getUsername(),
            'comment' => $this->getValue("comment"),
            'start_time' => $this->getValue("start")->getTimestamp(),
            'end_time' => $this->getValue("end")->getTimestamp(),
            'fixed' => $isFixed,
            // 'trigger_name' => null,
            'type' => $objectType,
            'filter' => $filter,
            // 'pretty' => true
        ];

        if ($objectType == DowntimePlannerUtil::TYPE_HOST) {
            $requestBody['all_services'] = !is_null($this->getValue("all_services"));
            $requestBody['child_options'] = $this->getValue("child_hosts");
        }

        if (!$isFixed) {
            $requestBody['duration'] =
                (float) $this->getElement('hours')->getValue() * 3600
                + (float) $this->getElement('minutes')->getValue() * 60;
        }

        return $requestBody;
    }

    protected function callCommandExecution($body)
    {
        return self::api()->post(
            self::EXECUTE_COMMAND_ENDPOINT,
            $body,
            [
                'Content-type' => 'application/json',
                'Cookie' => http_build_query($this->getRequest()->getCookieParams(), null, '; ')
            ]
        );
    }

    /**
     * @return resource
     */
    protected function api()
    {
        if ($this->api === null) {
            $configUtil = new CustomactionsConfigUtil();

            $this->api = new CustomRestApiClient(
                $configUtil->getIcinga2Host(),
                $configUtil->getDirectorIcinga2Username(),
                $configUtil->getDirectorIcinga2Password()
            );
            $this->api->disableSslPeerVerification();
            $this->api->setPort($configUtil->getIcinga2Port());
        }
        return $this->api;
    }

    protected function filtersContainHost()
    {
        $ret = false;
        foreach ($this->filters as $filter) {
            foreach ($filter as $type => $filter) {
                if ($type == DowntimePlannerUtil::TYPE_HOST && !is_null($filter)) {
                    $ret = true;
                }
            }
        }
        return $ret;
    }

    protected function redirectOnSuccess()
    {
        $url = $this->urlHelper->direct("index") . '?category=' . $this->getIcingaRequest()->getParam("category") . '#!' . $this->urlHelper->direct("index", "results");

        $this->getIcingaResponse()->redirectAndExit($url);
    }

    // END
}

function console_log($data)
{
    echo '<script>';
    echo 'console.log(' . json_encode($data) . ')';
    echo '</script>';
}
