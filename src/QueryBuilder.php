<?php
/**
 * @copyright Copyright (C) 2016-2017. Max Dark maxim.dark@gmail.com
 * @license MIT; see LICENSE.txt
 */

namespace useless\database;

interface QueryBuilder
{
    /**
     * QueryBuilder constructor.
     *
     * @param string $tableName
     */
    public function __construct($tableName);

    /**
     * @param string $indexKey
     *
     * @return string
     */
    public function deleteSQL($indexKey);

    /**
     * @param array  $names
     * @param string $indexKey
     *
     * @return string
     */
    public function updateSQL(array $names, $indexKey);

    /**
     * @param array $names
     * @param array $params
     *
     * @return string
     */
    public function insertSQL(array $names, array $params);

    /**
     * @param array $rule
     *
     * @return array sql+query params
     */
    public function selectSQL(array $rule);
}