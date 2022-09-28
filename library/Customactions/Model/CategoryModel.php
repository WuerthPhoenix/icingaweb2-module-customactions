<?php


namespace Icinga\Module\Customactions\Model;

use Icinga\Module\Neteye\Model\BaseModel;

class CategoryModel extends BaseModel
{
    /**
     * @var string $name
     * @db_column
     * @table_column
     * @search_column
     * @cli_create_mandatory
     * @translate_label Name
     * @form_input_type text
     * @translate_tooltip Name of the category
     */
    private $name;

     /**
     * @var string $description
     * @db_column
     * @table_column
     * @translate_label Description
     * @form_input_type text
     * @translate_tooltip Description of the category
     */
    private $description;

    /**
     * @var string $showAllServices
     * @db_column
     * @table_column
     * @translate_label show all services option
     * @form_input_type select
     * @form_input_options_static yes, no
     * @translate_tooltip decides if the all services downtime schedule option should be shown
     */
    private $showAllServices;

    /**
     * Customer constructor.
     * @param int $id
     * @param string $name
     * @param string $description
     */
    public function __construct(
        int $id = null,
        string $name,
        string $description = null,
        string $showAllServices = null
    ){
        $this->setId($id);
        $this->setName($name);
        $this->setDescription($description);
        $this->setShowAllServices($showAllServices);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

     /**
     * @return string
     */
    public function getShowAllServices()
    {
        return $this->showAllServices;
    }

    /**
     * @param string $showAllServices
     */
    public function setShowAllServices($showAllServices)
    {
        $this->showAllServices = $showAllServices;
    }

}