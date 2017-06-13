<?php
/**
 * @copyright Copyright (C) 2016-2017. Max Dark maxim.dark@gmail.com
 * @license MIT; see LICENSE.txt
 */

namespace useless\database;

/**
 * Class Config
 *
 * @package useless\database
 */
class Config
{
    /**
     * @var string DSN
     */
    private $dsn;

    /**
     * @var string Database User name
     */
    private $user;

    /**
     * @var string Password
     */
    private $password;

    /**
     * @var string Table Prefix
     */
    private $prefix;

    /**
     * Config constructor.
     *
     * @param string $dsn
     * @param string $user
     * @param string $password
     * @param string $prefix
     */
    public function __construct($dsn, $user, $password, $prefix = '')
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getDSN()
    {
        return $this->dsn;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
}
