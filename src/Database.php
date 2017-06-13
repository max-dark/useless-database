<?php
/**
 * @copyright Copyright (C) 2016-2017. Max Dark maxim.dark@gmail.com
 * @license MIT; see LICENSE.txt
 */

namespace useless\database;

/**
 * Class Database
 * @package useless\database
 */
class Database
{
    /**
     * @param Config|null $cfg
     * @return \PDO
     */
    public static function instance(Config $cfg = null)
    {
        static $link = null;
        if (is_null($link)) {
            $opt = [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ];
            $link = new \PDO($cfg->getDSN(), $cfg->getUser(), $cfg->getPassword(), $opt);
        }
        return $link;
    }
}
