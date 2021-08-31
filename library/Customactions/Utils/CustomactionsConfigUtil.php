<?php

namespace Icinga\Module\Customactions\Utils;

use Icinga\Application\Config;

class CustomactionsConfigUtil
{
    const MODULE_NAME = 'customactions';
    const API_USER_SECTION = 'apiuser';

    const USERNAME_KEY = 'username';
    const PASSWORD_KEY = 'password';
    const HOST_KEY = 'host';
    const PORT_KEY = 'port';

    const EMPTY_VALUE_MESSAGE = 'Error while retrieving value of key \'%s\' from customactions config file. Value must be set and be not empty';

    private $moduleConfig;

    public function __construct()
    {
        $this->setModuleConfig(Config::module(self::MODULE_NAME));
    }

    /**
     * @return string|null
     */
    public function getDirectorIcinga2Username(): ?string
    {
        $res = $this->getModuleConfig()->get(self::API_USER_SECTION, self::USERNAME_KEY);
        if (empty($res)) {
            throw new \Exception(sprintf(self::EMPTY_VALUE_MESSAGE, self::USERNAME_KEY));
        }
        return $res;
    }

    /**
     * @return string|null
     */
    public  function getDirectorIcinga2Password(): ?string
    {
        $res = $this->getModuleConfig()->get(self::API_USER_SECTION, self::PASSWORD_KEY);
        if (empty($res)) {
            throw new \Exception(sprintf(self::EMPTY_VALUE_MESSAGE, self::PASSWORD_KEY));
        }
        return $res;
    }

    /**
     * Returns the hostname of icinga2, e.g. `icinga2-master.neteyelocal`
     * @return string
     */
    public  function getIcinga2Host(): ?string
    {
        $res = $this->getModuleConfig()->get(self::API_USER_SECTION, self::HOST_KEY);
        if (empty($res)) {
            throw new \Exception(sprintf(self::EMPTY_VALUE_MESSAGE, self::HOST_KEY));
        }
        return $res;
    }


    public function getIcinga2Port(): ?string
    {
        $res = $this->getModuleConfig()->get(self::API_USER_SECTION, self::PORT_KEY);
        if (empty($res)) {
            throw new \Exception(sprintf(self::EMPTY_VALUE_MESSAGE, self::PORT_KEY));
        }
        return $res;
    }

    /**
     * @return Config
     */
    public function getModuleConfig(): Config
    {
        return $this->moduleConfig;
    }

    /**
     * @param Config $moduleConfig
     */
    public function setModuleConfig(Config $moduleConfig): void
    {
        $this->moduleConfig = $moduleConfig;
    }

}