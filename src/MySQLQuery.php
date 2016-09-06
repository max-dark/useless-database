<?php
/**
 * @copyright Copyright (C) 2016. Max Dark maxim.dark@gmail.com
 * @license MIT; see LICENSE.txt
 */

namespace useless\database;

class MySQLQuery implements QueryBuilder
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * QueryBuilder constructor.
     *
     * @param string $tableName
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param string $indexKey
     *
     * @return string
     */
    public function deleteSQL($indexKey)
    {
        /** @noinspection SqlResolve */
        return "delete from `{$this->getTableName()}` where `{$indexKey}` = :index_key limit 1";
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param array  $names
     * @param string $indexKey
     *
     * @return string
     */
    public function updateSQL(array $names, $indexKey)
    {
        $values = [];
        foreach ($names as $name) {
            $values[] = "`{$name}` = :{$name}";
        }
        $values = implode(', ', $values);

        /** @noinspection SqlResolve */
        return "update `{$this->getTableName()}` set {$values} where `{$indexKey}` = :{$indexKey} limit 1";
    }

    /**
     * @param array $names
     * @param array $params
     *
     * @return string
     */
    public function insertSQL(array $names, array $params)
    {
        $names  = implode(',', $names);
        $values = implode(',', array_keys($params));

        /** @noinspection SqlResolve */
        return "insert into `{$this->getTableName()}` ({$names}) values({$values})";
    }

    /**
     * @param array $rule
     *
     * @return array sql+query params
     */
    public function selectSQL(array $rule)
    {
        /** @noinspection SqlResolve */
        $select   = "select * from `{$this->getTableName()}`";
        $params   = [];
        $order_by = [];
        $where    = [];
        $limit    = '';
        $offset   = '';
        foreach ($rule as $name => $value) {
            switch ($name) {
                case 'limit':
                    $limit = ' limit ' . (int)$value;
                break;
                case 'offset':
                    $offset = ' offset ' . (int)$value;
                break;
                case 'order':
                    $order_by[] = "`{$value[0]}` {$value[1]}";
                break;
                default:
                    $where[]            = "`$name` = :{$name}";
                    $params[":{$name}"] = $value;
                break;
            }
        }
        $where    = $where ? ' where ' . implode(' and ', $where) : '';
        $order_by = $order_by ? ' order by ' . implode(', ', $order_by) : '';

        $sql = $select . $where . $order_by . $limit . $offset;

        return [
            $sql,
            $params
        ];
    }
}