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
     * Customer constructor.
     * @param int $id
     * @param string $name
     * @param string $description
     */
    public function __construct(
        int $id = null,
        string $name,
        string $description = null
    ){
        $this->setId($id);
        $this->setName($name);
        $this->setDescription($description);
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

}