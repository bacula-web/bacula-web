<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2023 Davide Franco
 *
 * This file is part of Bacula-Web.
 *
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see
 * <https://www.gnu.org/licenses/>.
 */

namespace Core\Db;

use App\Libs\FileConfig;
use Core\App\CErrorHandler;
use PDO;

class Database
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $driver;

    /**
     * @param int $catalogId
     * @throws \Exception
     */
    public function __construct(int $catalogId = null)
    {
        $username = null;
        $password = null;

        if ($catalogId !== null) {
            FileConfig::open(CONFIG_FILE);

            $this->driver = FileConfig::get_Value('db_type', $catalogId);
            $dsn = FileConfig::get_DataSourceName($catalogId);

            // Bacula catalog is not using SQLite
            if ($this->driver != 'sqlite') {
                $username = FileConfig::get_Value('login', $catalogId);
                $password  = FileConfig::get_Value('password', $catalogId);
            }
        } else {
            $this->driver = 'sqlite';
            $dsn = $this->driver . ':' . BW_ROOT . '/application/assets/protected/application.db';
        }

        $options = [
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        if ($this->driver == 'mysql') {
            $options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
        }

        $this->connection = new PDO(
            $dsn,
            $username,
            $password,
            $options
        );
    }

    /**
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getDriverName(): string
    {
        return $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * @return mixed|string
     */
    public function getServerVersion()
    {
        $server_version = $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
        $server_version = explode(':', $server_version);
        return $server_version[0];
    }

    /**
     * Return database server timestamp
     *
     * @return int
     */
    public function getServerTimestamp(): int
    {
        // TODO: remove support for SQLite
        if ($this->getDriverName() == 'sqlite') {
            $statment = "SELECT datetime('now') as currentdatetime";
        } else {
            $statment = 'SELECT now() as currentdatetime';
        }

        $result = $this->connection->query($statment);
        $result = $result->fetch();

        // Return timestamp
        return strtotime($result['currentdatetime']);
    } // end function getServerTimestamp()
}
