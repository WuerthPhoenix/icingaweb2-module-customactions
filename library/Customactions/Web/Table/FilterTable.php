<?php

namespace Icinga\Module\Customactions\Web\Table;

use Icinga\Module\Customactions\Repository\CategoryRepository;
use Icinga\Module\Neteye\Web\Table\BaseTable;

class FilterTable extends BaseTable
{
    protected $categoryRendering = [];

    public function __construct($models)
    {

        // construct the $categoryRendering dictionary in order to map the category id to a human-readable string
        // which is then used by renderCategoryIdValue()
        $categoryRepository = new CategoryRepository();
        $categoryModelObjects = $categoryRepository->findAll();

        foreach ($categoryModelObjects as $category) {
            $this->categoryRendering[$category->getId()] = $category->getName();
        }

        parent::__construct($models);
    }

    /*
     * this function is used by BaseTable to transform the category id to the category object name
     */
    public function renderCategoryIdValue($value)
    {
        return $this->categoryRendering[$value];
    }

    /*
     * this function is used by BaseTable to transform the table header category_id
     */
    public function renderCategoryIdHeader()
    {
        return 'Category';
    }
}
