<?php


namespace Icinga\Module\Customactions\Model;

use Icinga\Module\Neteye\Model\BaseModel;

class ScheduleDowntimeModel extends BaseModel
{

    /**
     * @var string $name
     * @db_column
     * @table_column
     * @translate_label Name
     */
    private $name;

    /**
     * @var string $type
     * @db_column
     * @table_column
     * @translate_label Type
     */
    private $type;

    /**
     * @db_column
     * @var string $body
     * @translate_label Api Call
     */
    private $body;

    /**
     * @db_column
     * @var string $message
     * @table_column
     * @translate_label Status
     */
    private $message;

    /**
     * @db_column
     * @var int $statusCode
     * @table_column
     * @translate_label statusCode
     */
    private $statusCode;


    /**
     * Contract constructor.
     * @param int $id
     * @param string $name
     * @param string $type
     * @param string $body
     * @param string $message
     * @param int $statusCode
     * @throws \Exception
     */
    public function __construct(
        int $id,
        string $name,
        string $type,
        string $body,
        string $message,
        int $statusCode
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setType($type);
        $this->setBody($body);
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
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body = null): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message = null): void
    {
        $this->message = $message;
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
    public function setStatusCode(string $statusCode = null): void
    {
        $this->statusCode = $statusCode;
    }
}