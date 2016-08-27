<?php
/**
 * @copyright Copyright (C) 2016. Max Dark maxim.dark@gmail.com
 * @license MIT; see LICENSE.txt
 */

namespace useless\database;

use useless\abstraction\Model;
use useless\abstraction\Storage;

/**
 * Class MySqlStorage
 * Implementation for Storage interface
 * @package useless\database
 */
class MySqlStorage implements Storage
{
    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $tablePrefix = '';

    /**
     * @var string
     */
    private $tableName = '';

    /**
     * @var \useless\abstraction\Storage[]
     */
    private static $instances = [];

    /**
     * MySqlStorage constructor.
     *
     * @param string $tablePrefix
     * @param \PDO $pdo
     */
    public function __construct($tablePrefix, $pdo)
    {
        $this->pdo = $pdo;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @param string $modelClass
     * @return \useless\abstraction\Storage
     */
    public function forModel($modelClass)
    {
        if (!array_key_exists($modelClass, static::$instances)) {
            $instance = new static($this->tablePrefix, $this->pdo);
            $instance->modelClass = $modelClass;
            $instance->tableName = $this->tablePrefix . call_user_func([$modelClass, 'name']);
            static::$instances[$modelClass] = $instance;
        }
        return static::$instances[$modelClass];
    }

    /**
     * @param array $rule
     * @return \useless\abstraction\Result
     */
    public function findOne(array $rule)
    {
        $rule['limit'] = 1;
        return $this->findMany($rule);
    }

    /**
     * @param array $rule
     * @return \useless\abstraction\Result
     */
    public function findMany(array $rule)
    {
        list($sql, $params) = $this->buildQuery($rule);
        return $this->buildResult($sql, $params);
    }

    /**
     * @param \useless\abstraction\Model $model
     * @param string $indexKey
     * @return bool
     */
    public function save(Model $model, $indexKey = 'uid')
    {
        $fields = $model->getFields();
        $index = $fields[$indexKey];
        unset($fields[$indexKey]);
        $names = array_keys($fields);
        $params = [];
        foreach ($fields as $name => $value) {
            $params[":{$name}"] = $value;
        }
        $doInsert = empty($index);
        if ($doInsert) {
            // insert
            $names = implode(',', $names);
            $values = implode(',', array_keys($params));
            /** @noinspection SqlResolve */
            $sql = "insert into `{$this->tableName}` ({$names}) values({$values})";
        } else {
            // update
            $values = [];
            foreach ($names as $name) {
                $values[] = "`{$name}` = :{$name}";
            }
            $values = implode(', ', $values);
            $params[":{$indexKey}"] = $index;
            /** @noinspection SqlResolve */
            $sql = "update `{$this->tableName}` set {$values} where `{$indexKey}` = :{$indexKey} limit 1";
        }
        /** @var \PDOStatement $query */
        $query = $this->pdo->prepare($sql);
        $success = $query->execute($params);
        if ($success && $doInsert) {
            $model->setUID($this->pdo->lastInsertId());
        }
        return $success;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return \useless\abstraction\Result
     */
    private function buildResult($sql, $params = [])
    {
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        return new DbResult($query, $this->modelClass);
    }

    /**
     * @param array $rule
     * @return array sql+query params
     */
    private function buildQuery(array $rule)
    {
        /** @noinspection SqlResolve */
        $select = "select * from `{$this->tableName}`";
        $params = [];
        $order = '';
        $where = [];
        $limit = '';
        foreach ($rule as $name => $value) {
            switch ($name) {
                case 'limit':
                    $limit = ' limit ' . (int)$value;
                    break;
                case 'order':
                    $order = " order by `{$value[0]}` {$value[1]}";
                    break;
                default:
                    $where[] = "`$name` = :{$name}";
                    $params[":{$name}"] = $value;
                    break;
            }
        }
        if ($where) {
            $where = ' where ' . implode(' and ', $where);
        }
        return [
            $select . $where . $order . $limit,
            $params
        ];
    }

    /**
     * @param \useless\abstraction\Model $model
     * @param string $indexKey
     * @return bool
     */
    public function remove(Model $model, $indexKey = 'uid')
    {
        /** @noinspection SqlResolve */
        $sql = "delete from `{$this->tableName}` where `{$indexKey}` = :index_key limit 1";
        $query = $this->pdo->prepare($sql);
        return $query->execute([':index_key' => $model->getUID()]);
    }
}
