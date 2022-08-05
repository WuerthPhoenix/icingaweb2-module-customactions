<?php


namespace Icinga\Module\Customactions\Model;

use Icinga\Module\Neteye\Model\BaseModel;

class ApiResultModel extends BaseModel
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
     * @var string $message
     * @table_column
     * @translate_label Message
     */
    private $message;

    /**
     * @var int $statusCode
     * @table_column
     * @translate_label StatusCode
     */
    private $statusCode;


    /**
     * Contract constructor.
     * @param int $id
     * @param string $name
     * @param string $type
     * @param string $filter
     * @param string $message
     * @param int $statusCode
     * @throws \Exception
     */
    public function __construct(
        int $id,
        string $name,
        string $type,
        string $filter,
        string $message,
        int $statusCode
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setType($type);
        $this->setFilter($filter);
        $this->setMessage($message);
        $this->setStatusCode($statusCode);
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
    public function getMessage(): string
    {
        return $this->status;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message= null): void
    {
        $this->message= $message;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode = null): void
    {
        $this->statusCode = $statusCode;
    }
}