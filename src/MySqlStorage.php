<?php
/**
 * @copyright Copyright (C) 2016. Max Dark maxim.dark@gmail.com
 * @license MIT; see LICENSE.txt
 */

namespace useless\database;

use useless\abstraction\Storage;

/**
 * Class MySqlStorage
 * Implementation for Storage interface
 *
 * @package useless\database
 */
class MySqlStorage extends SqlStorage implements Storage
{
    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return new MySQLQuery($this->getTableName());
    }
}
