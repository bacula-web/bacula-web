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

use Core\Exception\DatabaseException;
use PDO;
use Exception;
use PDOException;
use PDOStatement;

class Table
{
    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * @var Database
     */
    protected Database $db;

    /**
     * @var string
     */
    protected string $driver;

    /**
     * @var
     */
    protected $parameters;

    /**
     * @var null
     */
    protected ?string $tablename = null;

    /**
     * @param Database $db
     * @throws Exception
     */
    public function __construct(Database $db)
    {
        if ($this->tablename === null) {
            throw new DatabaseException("\$tablename property is not set in " . static::class . ' class');
        }

        // Get PDO instance
        $this->db = $db;
        $this->pdo = $this->db->getDb();
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tablename;
    }

    /**
     * Return table row count or 0
     *
     * @param $filter
     * @return int
     * @throws Exception
     */
    public function count($filter = null): int
    {
        $fields = array( 'COUNT(*) as row_count' );

        // Prepare and execute query
        $statement = CDBQuery::get_Select([
                'table' => $this->tablename,
                'fields' => $fields,
                'where' => $filter
            ]);

        $result = $this->select($statement, null, null, true);

        // If SQL count result is null, return 0 instead (much better when plotting data)
        if (is_null($result['row_count'])) {
            return 0;
        } else {
            return (int)$result['row_count'];
        }
    }

    /**
     * @return mixed
     */
    public function get_driver_name(): string
    {
        return $this->db->getDriverName();
    }

    /**
     * @param string $query
     * @param array|null $params
     * @param string|null $fetchClass
     * @param boolean $single
     * @return mixed
     */
    public function select(string $query, array $params = null, string $fetchClass = null, $single = null)
    {
        if ($params !== null) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($params);
        } else {
            $statement = $this->pdo->query($query);
        }

        if ($fetchClass !== null) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $fetchClass);
        }

        if ($single !== null) {
            return $statement->fetch();    // set fetch mode
        }

        return $statement->fetchAll();
    }

    /**
     * Prepare a query using PDO::prepare() and return false on failure, or a PDOStatement
     * @param string $query SQL query
     * @param array|null $params
     * @return PDOStatement|bool
     */
    protected function execute(string $query, array $params = null)
    {
        $statement = $this->pdo->prepare($query);
        if ($params !== null) {
            $statement->execute($params);
        } else {
            $statement->execute();
        }
        return $statement;
    }

    /**
     * @param $query
     * @return PDOStatement
     */
    public function run_query($query): PDOStatement
    {
        // Prepare PDO statement
        $statment = $this->pdo->prepare($query);

        if ($statment === false) {
            throw new DatabaseException("Failed to prepare PDOStatment <br />$query");
        }

        // Bind PHP variables with named placeholders
        if (isset($this->parameters)) {
            try {
                foreach ($this->parameters as $name => $value) {
                    if (is_string($value)) {
                        $statment->bindValue(":$name", $value, PDO::PARAM_STR);
                    } elseif (is_int($value)) {
                        $statment->bindValue(":$name", $value, PDO::PARAM_INT);
                    } elseif (is_bool($value)) {
                        $statment->bindValue(":$name", $value, PDO::PARAM_BOOL);
                    }
                }
            } catch (PDOException $pdoException) {
                $pdoException->getMessage();
            }
        }

        $result = $statment->execute();

        /**
        * Reset $this->parameters to an empty array
        * Otherwise, next call to Table::run_query() will fail if Table::addParameters()
        * is not called and Table::parameters is not empty
        */
        $this->parameters = [];

        if ($result === false) {
            throw new PDOException("Failed to execute PDOStatment <br />$query");
        } else {
            return $statment;
        }
    }

    /**
     * addParameter
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function addParameter(string $name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Return PDO connection status or null
     *
     * @return string|null
     */
    public function getConnectionStatus(): ?string
    {
        // If MySQL of postGreSQL
        if ($this->get_driver_name() != 'sqlite') {
            return $this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        } else {
            return 'N/A';
        }
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        // If MySQL of postGreSQL
        switch ($this->get_driver_name()) {
            case 'mysql':
            case 'pgsql':
                $pdo_connection = $this->getConnectionStatus();
                break;
            default:
               // We assume that the user running Apache has access to the SQLite database file (must be improved)
                $pdo_connection = true;
        }

        // Test connection status
        if ($pdo_connection !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $criteria
     * @param $parameters
     * @return array|bool
     */
    public function find($criteria, $parameters = null)
    {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ';
        $sql .= implode(' AND ', $criteria);

        $statement = $this->execute($sql, $parameters);

        return $statement->fetch();
    }

    /**
     * @param $sql
     * @param $parameters
     * @param $fetchClass
     * @return array|false
     */
    public function findAll($sql, $parameters, $fetchClass)
    {
        $statement = $this->execute($sql, $parameters);

        return $statement->fetchAll(PDO::FETCH_CLASS, $fetchClass);
    }
}
