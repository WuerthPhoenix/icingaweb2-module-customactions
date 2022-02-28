<?php

namespace Icinga\Module\Customactions\Repository;

use Icinga\Module\Customactions\Utils\UserUtil;
use Icinga\Module\Neteye\Repository\BaseLoggingRepository;
use Icinga\Security\SecurityException;

class CategoryRepository extends BaseLoggingRepository
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

    public function getCategoryList($displayAttribute)
    {
        $categoriesData = [];
        $categoriesObject = $this->findAll();
        if (!empty($categoriesObject)) {
            /* @var FilterModel $categoriesObject */
            foreach ($categoriesObject as $categoriesObject) {
                $categoriesData[$categoriesObject->getId()] = $categoriesObject->getValue($displayAttribute);
            }
        }
        return $categoriesData;
    }

    public function findCategoryByIdWithRestriction($id)
    {
        $where = ['id' => $id];
        $where = $this->addFilterCondition($where);
        $category = $this->findAllByFilters($where);
        if (!empty($category)) {
            $category = $category[0];
        } else {
            throw new SecurityException('No permission for this category');
        }
        return $category;
    }

    public function findCategoriesWithRestriction()
    {
        $where = $this->addFilterCondition();
        $categories = $this->findAllByFilters($where);
        return $categories;
    }

    public function findCategoryIdsWithRestriction()
    {
        $categoryIds = [];
        $where = $this->addFilterCondition();
        $categories = $this->findAllByFilters($where);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $categoryIds[] = $category->getId();
            }
        }
        return $categoryIds;
    }

    public function userAccessValidationForCategoryObject($id)
    {
        $repository = new self();
        $categoriesId = $repository->findCategoryIdsWithRestriction();
        return in_array($id, $categoriesId);
    }

    /**
     * @param $where
     *
     * @return mixed
     */
    protected function addFilterCondition($where = [])
    {
        $userFilters = UserUtil::getUserRestrictionsForCategoryBox();
        if (!empty($userFilters)) {
            $where['name'] = $userFilters;
        }
        return $where;
    }
}
