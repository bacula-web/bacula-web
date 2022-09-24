<?php

namespace Core\Db;

class DatabaseFactory
{
    /**
     * @param $dsn|null
     * @return Database
     */
    public static function getDatabase($dsn = null) :Database
    {
        return new Database($dsn);
    }
}
