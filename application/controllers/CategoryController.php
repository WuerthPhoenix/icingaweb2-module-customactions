<?php


namespace Icinga\Module\Customactions\Controllers;

use Icinga\Module\Customactions\Repository\CategoryRepository;
use Icinga\Module\Customactions\Utils\PermissionUtil;
use Icinga\Module\Neteye\Controllers\BaseModelController;

/**
 * Class CategoryController
 * @package Icinga\Module\Customactions\Controllers
 * @related_object Category
 */
class CategoryController extends BaseModelController
{
    public function init()
    {
        PermissionUtil::isAllowedForAdmin();
        parent::init();
    }

    /**
     * This method will get the filter condition set in the URL and can be overridden in the child class to get specific
     * filters
     * @param $filterConditions
     */
    protected function getFilterConditionsFromUrl(&$filterConditions)
    {
        $categoryRepository = new CategoryRepository();
        $categoriesId = $categoryRepository->findCategoryIdsWithRestriction();
        $filterConditions = ["id" => $categoriesId];
    }
}
