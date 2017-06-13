<?php
/**
 * @copyright Copyright (C) 2016-2017. Max Dark maxim.dark@gmail.com
 * @license MIT; see LICENSE.txt
 */

namespace useless\database;

use useless\abstraction\Model;
use useless\abstraction\Result;

/**
 * Class DbResult
 * @package useless\database
 */
class DbResult implements Result
{
    /**
     * @var \PDOStatement
     */
    private $data;

    /**
     * @var string Model/DTO class name
     */
    private $className;

    /**
     * DbResult constructor.
     *
     * @param \PDOStatement $data
     * @param string $className
     */
    public function __construct(\PDOStatement $data, $className)
    {
        $this->data = $data;
        $this->className = $className;
    }

    /**
     * @return Model|false single Model from result
     */
    public function getOne()
    {
        return $this->data->fetchObject($this->className);
    }

    /**
     * @return \useless\abstraction\Model[]|false all matched models from result
     */
    public function getAll()
    {
        return $this->data->fetchAll(\PDO::FETCH_CLASS, $this->className);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->data->fetchAll(\PDO::FETCH_ASSOC);
    }
}
