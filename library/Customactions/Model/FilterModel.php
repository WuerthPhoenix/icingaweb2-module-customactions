<?php


namespace Icinga\Module\Customactions\Model;

use Icinga\Module\Neteye\Model\BaseModel;
use Icinga\Module\Customactions\Repository\CategoryRepository;

class FilterModel extends BaseModel
{

    /**
     * @var string $name
     * @db_column
     * @table_column
     * @search_column
     * @cli_create_mandatory
     * @translate_label Name
     * @form_input_type text
     * @translate_tooltip Name of the filter
     */
    private $name;

    /**
     * @var string $description
     * @db_column
     * @table_column
     * @translate_label Description
     * @form_input_type text
     * @translate_tooltip Description of the filter
     */
    private $description;

    /**
     * @var string $filterHost
     * @db_column
     * @table_column
     * @translate_label Hostfilter
     * @form_input_type text
     * @translate_tooltip Filter to apply for hosts
     */
    private $filterHost;

    /**
     * @var string $filterService
     * @db_column
     * @table_column
     * @translate_label Servicefilter
     * @form_input_type text
     * @translate_tooltip Filter to apply for services
     */
    private $filterService;

    /**
     * @var int $categoryId
     * @db_column
     * @table_column
     * @cli_create_mandatory
     * @translate_label Category
     * @form_input_type select
     * @form_input_options_from_db Icinga\Module\Customactions\Repository\CategoryRepository
     * @form_input_options_display_attr name
     * @translate_tooltip Category the filter belongs to
     */
    private $categoryId;


    /**
     * Contract constructor.
     * @param int $id
     * @param string $name
     * @param string $description
     * @param string $filterHost
     * @param string $filterService
     * @param int $categoryId
     * @throws \Exception
     */
    public function __construct(
        int $id = null,
        string $name,
        string $description = null,
        string $filterHost = null,
        string $filterService = null,
        int $categoryId

    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setDescription($description);
        $this->setFilterHost($filterHost);
        $this->setFilterService($filterService);
        $this->setCategoryId($categoryId);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description = null): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFilterHost(): ?string
    {
        return $this->filterHost;
    }

    /**
     * @param string $filterHost
     */
    public function setFilterHost(string $filterHost = null): void
    {
        $this->filterHost = $filterHost;
    }

    /**
     * @return string
     */
    public function getFilterService(): ?string
    {
        return $this->filterService;
    }

    /**
     * @param string $filterService
     */
    public function setFilterService(string $filterService = null): void
    {
        $this->filterService = $filterService;
    }
    
    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return CategoryModel
     */
    public function getCategory(): CategoryModel
    {
        $categoryRepository = new CategoryRepository();
        return $categoryRepository->findById($this->getCategoryId());
    }

}