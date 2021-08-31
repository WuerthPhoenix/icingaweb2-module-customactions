<?php

namespace Icinga\Module\Customactions\Repository;

use Icinga\Module\Customactions\Utils\DeployLoggingUtil;
use Icinga\Module\Neteye\Utils\BaseFormUtil;
use Icinga\Module\Neteye\Model\BaseModel;
use Icinga\Module\Neteye\Repository\BaseRepository;

class BaseActionLoggingRepository extends BaseRepository
{
    protected $objectNameColumn = 'name';
    protected $baseFormUtil;
    protected $id;

    public function __construct($moduleName = null)
    {
        $this->baseFormUtil = new BaseFormUtil();
        parent::__construct($moduleName);
    }

    /**
     * @return string
     */
    public function getObjectNameColumn()
    {
        return $this->objectNameColumn;
    }

    /**
     * @param string $objectNameColumn
     */
    public function setObjectNameColumn($objectNameColumn)
    {
        $this->objectNameColumn = $objectNameColumn;
    }

    public function add(BaseModel $modelObject)
    {
        // $this->id = parent::add($modelObject);
        // $newValues = $this->getObjectValuesById($this->id);
        $newValues = $modelObject->getValues();
        $this->registerAuditLogDeployAction($newValues
        // , 'edit'
    );
        // return $this->id;
    }

    // public function update(BaseModel $modelObject)
    // {
    //     $this->id = $modelObject->getId();
    //     $oldValues = $this->getObjectValuesById($modelObject->getId());
    //     parent::update($modelObject);
    //     $this->registerAuditLogModifyAction($oldValues, $modelObject->getvalues(), 'edit');
    // }

    // public function delete(BaseModel $modelObject)
    // {
    //     parent::delete($modelObject);
    //     $this->registerAuditLogDeleteAction($modelObject->getValues());
    // }

    public function setAuditLogDeploy($paramsToAuditlogFunctions)
    {
        DeployLoggingUtil::sendDeployAuditLog(
            $this->getModuleName(),
            $this->getObjectType(),
            $paramsToAuditlogFunctions['objectName'],
            $paramsToAuditlogFunctions['url'],
            $paramsToAuditlogFunctions['newValues'],
            $paramsToAuditlogFunctions['username'],
            $paramsToAuditlogFunctions['message']
        );
    }

    // public function setAuditLogModify($paramsToAuditlogFunctions)
    // {
    //     BaseLoggingUtil::sendModifyAuditLog(
    //         $this->getModuleName(),
    //         $this->getObjectType(),
    //         $paramsToAuditlogFunctions['objectName'],
    //         $paramsToAuditlogFunctions['url'],
    //         $paramsToAuditlogFunctions['oldValues'],
    //         $paramsToAuditlogFunctions['newValues'],
    //         $paramsToAuditlogFunctions['username'],
    //         $paramsToAuditlogFunctions['message']
    //     );
    // }

    // public function setAuditLogDelete($paramsToAuditlogFunctions)
    // {
    //     BaseLoggingUtil::sendDeleteAuditLog(
    //         $this->getModuleName(),
    //         $this->getObjectType(),
    //         $paramsToAuditlogFunctions['objectName'],
    //         $paramsToAuditlogFunctions['newValues'],
    //         $paramsToAuditlogFunctions['username'],
    //         $paramsToAuditlogFunctions['message']
    //     );
    // }

    protected function getAuditlogUsername(&$paramsToAuditlogFunctions)
    {
        if (DeployLoggingUtil::isCli()) {
            $paramsToAuditlogFunctions['username'] = 'cli';
        }
    }

    protected function getAuditlogMessage(&$paramsToAuditlogFunctions)
    {
    }

    protected function getAuditlogObjectUrl(&$paramsToAuditlogFunctions)
    {
        if (DeployLoggingUtil::isCli()) {
            $paramsToAuditlogFunctions['url'] = '';
        } elseif (!empty($paramsToAuditlogFunctions['action'])) {
            $paramsToAuditlogFunctions['url'] = $this->baseFormUtil->getUrl($paramsToAuditlogFunctions['action'], [
                'id' => $this->id
            ]);
        }
    }

    protected function getAuditlogObjectNameColumn(&$paramsToAuditlogFunctions)
    {
        if (isset($paramsToAuditlogFunctions['newValues'][$this->getObjectNameColumn()])) {
            $paramsToAuditlogFunctions['objectName'] = $paramsToAuditlogFunctions['newValues'][$this->getObjectNameColumn()];
        } else {
            throw new \InvalidArgumentException(sprintf('No %s column found', $this->getObjectNameColumn()));
        }
    }

    /**
     * @param array $newValues
     * @param array $oldValues
     * @param null $action
     * @return array
     */
    protected function getParamsForAuditlogFunctions($action = null, $newValues = [], $oldValues = [])
    {
        // Use stripclashes depending if body should contain escaped backslashes or not
        $newValues["body"] = stripcslashes($newValues["body"]);
        unset($newValues["id"]);
        $paramsToAuditlogFunctions = [
            'action' => $action,
            'oldValues' => '',
            'newValues' => $newValues,
            'username' => '',
            'url' => '',
            'objectName' => '',
            'message' => ''
        ];

        $this->getAuditlogObjectNameColumn($paramsToAuditlogFunctions);
        $this->getAuditlogUsername($paramsToAuditlogFunctions);
        $this->getAuditlogMessage($paramsToAuditlogFunctions);
        if (!empty($action)) {
            $this->getAuditlogObjectUrl($paramsToAuditlogFunctions);
        }
        return $paramsToAuditlogFunctions;
    }

    // protected function registerAuditLogDeleteAction($oldValues, $action = null)
    // {
    //     $paramsToAuditlogFunctions = $this->getParamsForAuditlogFunctions($action, [], $oldValues);
    //     $this->setAuditLogDelete($paramsToAuditlogFunctions);
    // }

    protected function registerAuditLogDeployAction($newValues, $action = null)
    {
        $paramsToAuditlogFunctions = $this->getParamsForAuditlogFunctions($action, $newValues);
        $this->setAuditLogDeploy($paramsToAuditlogFunctions);
    }

    // protected function registerAuditLogModifyAction($oldValues, $newValues, $action = null)
    // {
    //     $paramsToAuditlogFunctions = $this->getParamsForAuditlogFunctions($action, $newValues, $oldValues);
    //     $this->setAuditLogModify($paramsToAuditlogFunctions);
    // }

    // protected function getObjectValuesById($id)
    // {
    //     return $this->findById($id)->getValues();
    // }
}