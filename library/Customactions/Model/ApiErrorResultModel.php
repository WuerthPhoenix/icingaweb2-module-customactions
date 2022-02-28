<?php


namespace Icinga\Module\Customactions\Model;

use Icinga\Module\Neteye\Model\BaseModel;

class ApiErrorResultModel extends BaseModel
{

    /**
     * @var string $name
     * @table_column
     * @translate_label Name
     */
    private $name;

    /**
     * @var string $type
     * @table_column
     * @translate_label Type
     */
    private $type;

    /**
     * @var string $filter
     * @translate_label Filter
     */
    private $filter;

    /**
     * @var string $status
     * @table_column
     * @translate_label Status
     */
    private $status;

    /**
     * @var int $error
     * @table_column
     * @translate_label Error
     */
    private $error;


    /**
     * Contract constructor.
     * @param int $id
     * @param string $name
     * @param string $type
     * @param string $filter
     * @param string $status
     * @param int $error
     * @throws \Exception
     */
    public function __construct(
        int $id,
        string $name,
        string $type,
        string $filter,
        string $status,
        int $error
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setType($type);
        $this->setFilter($filter);
        $this->setStatus($status);
        $this->setError($error);
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type = null): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return $this->filter;
    }

    /**
     * @param string $filter
     */
    public function setFilter(string $filter = null): void
    {
        $this->filter = $filter;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status = null): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error = null): void
    {
        $this->error = $error;
    }
}