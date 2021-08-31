<?php

namespace Icinga\Module\Customactions\Repository;

use Icinga\Module\Customactions\Utils\DowntimePlannerUtil;
use Icinga\Module\Neteye\Repository\BaseLoggingRepository;
use Icinga\Util\Translator;

class FilterRepository extends BaseLoggingRepository
{
    const MODULE_NAME = 'customactions';

    /**
     * FilterRepository constructor.
     * The parent constructor is called passing the module name as parameter.
     * This will allow the repository to be reachable by hooks.
     */
    public function __construct()
    {
        parent::__construct(self::MODULE_NAME);
    }

    /**
     * Overrides function to return a list
     * Overrides function to return a list of models according to the implementation of BaseRepository and BaseModel,
     * but the data is sorted ascending by name.
     *
     * @param array $conditions
     * @param null $searchValue
     * @return array<BaseModel> || array
     * @throws ReflectionException
     */
    public function findAllByFilters(array $conditions = [], $searchValue = null, $applyPrepareQueryFilterModifier = true): array
    {
        $result = [];
        $orderByParams = $this->getOrderByParams();
        $dbResult = $this->dbSelect($this->prepareQuery('*', $conditions, $searchValue, null, null, $orderByParams, $applyPrepareQueryFilterModifier));
        if (!empty($dbResult)) {
            $result = $this->convertToModelObjects($dbResult);
        }

        return $result;
    }

    /**
     * Overrides function to fetch the filter data order by name in ASC order.
     * @param null $value
     * @param null $direction
     * @return array
     */
    public function getOrderByParams($value = null, $direction = null)
    {
        return ['name' => SORT_ASC];
    }

    public function getFilterListByCategory($category, $displayAttribute)
    {
        $filtersData = [];
        $filtersObject = $this->findAllByFilters(["category_id" => $category]);
        if (!empty($filtersObject)) {
            /* @var FilterModel $filtersObject */
            foreach ($filtersObject as $filtersObject) {
                $filtersData[$filtersObject->getId()] = $filtersObject->getValue($displayAttribute);
            }
        }
        return $filtersData;
    }

    public function getFiltersByCondition(array $conditions = [])
    {
        $filtersData = [];
        $filtersObject = $this->findAllByFilters($conditions);
        if (!empty($filtersObject)) {
            /* @var FilterModel $filtersObject */
            foreach ($filtersObject as $filtersObject) {
                $filtersData[$filtersObject->getName()] = ["Host" => $filtersObject->getValue("filterHost"), "Service" => $filtersObject->getValue("filterService")];
            }
        }
        return $filtersData;
    }

    /**
     * @return array
     * @throws ProgrammingError
     */
    public function getTypeOptions()
    {
        $selectOptions[DowntimePlannerUtil::FIXED] = Translator::translate('Fixed', "customactions");
        $selectOptions[DowntimePlannerUtil::FLEXIBLE] = Translator::translate('Flexible', "customactions");

        return $selectOptions;
    }

    /**
     * @return array
     * @throws ProgrammingError
     */
    public function getChildHostsOptions()
    {
        $selectOptions[DowntimePlannerUtil::DOWNTIME_NO_CHILDFREN] = Translator::translate('Do nothing with child hosts', "monitoring");
        $selectOptions[DowntimePlannerUtil::DOWNTIME_TRIGGERED_CHILDREN] = Translator::translate('Schedule triggered downtime for all child hosts', "monitoring");
        $selectOptions[DowntimePlannerUtil::DOWNTIME_NON_TRIGGERED_CHILDREN] = Translator::translate('Schedule non-triggered downtime for all child hosts', "monitoring");

        return $selectOptions;
    }

    public function userAccessValidationForFilterObject($id)
    {
        $where = ['id' => $id];
        $categoryRepository = new CategoryRepository();
        $categoriesId = $categoryRepository->findCategoryIdsWithRestriction();
        $where["category_id"] = $categoriesId;
        $filters = $this->findAllByFilters($where);
        return !empty($filters);
    }
}


function console_log($data)
{
    echo '<script>';
    echo 'console.log(' . json_encode($data) . ')';
    echo '</script>';
}
