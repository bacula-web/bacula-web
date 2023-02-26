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

namespace App\Tables;

use App\Entity\User;
use Core\App\CErrorHandler;
use Core\Db\Table;
use PDOStatement;

class UserTable extends Table
{
    protected ?string $tablename = 'Users';

    /**
     * @param $username
     * @return mixed
     */
    public function findByName($username)
    {
        try {
            $sqlQuery = "SELECT * FROM 'Users' WHERE username = :username";

            return $this->select(
                $sqlQuery,
                ['username' => $username],
                '\App\Entity\User',
                true
            );
        } catch (\PDOException $e) {
            CErrorHandler::displayError($e);
        }
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        $getUsersQuery = "SELECT username,email FROM Users";

        return $this->select($getUsersQuery, null, '\App\Entity\User');
    }

    /**
     * @param string $username
     * @param string $password
     * @return PDOStatement|bool
     */
    public function setPassword(string $username, string $password)
    {
        $user = $this->findByName($username);

        $user->setPassword($password);

        $parameters = [
            'username' => $user->getUsername(),
            'hashedPassword' => $user->getPasswordHash()
        ];

        $query = 'UPDATE ' . $this->tablename . ' SET passwordHash = :hashedPassword WHERE username = :username';

        return $this->execute($query, $parameters);
    }

    /**
     * @param $username
     * @param $email
     * @param $password
     * @return bool|int
     */
    public function addUser($username, $email, $password)
    {
        $user = new User();

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);

        $parameters = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'hashedPassword' => $user->getPasswordHash()
        ];

        $addUserQuery = "INSERT INTO Users (username,email,passwordHash) VALUES (:username, :email, :hashedPassword)";

        return $this->execute($addUserQuery, $parameters);
    }

    /**
     * @return false|int
     */
    public function createSchema()
    {
        $createSchemaQuery = 'CREATE TABLE IF NOT EXISTS Users (
                        user_id INTEGER PRIMARY KEY,
                        username TEXT NOT NULL UNIQUE,
                        passwordHash TEXT NOT NULL,
                        email TEXT
                        );
                        CREATE INDEX IF NOT EXISTS User_ix_username ON Users (username);';

        return $this->pdo->exec($createSchemaQuery);
    }
}
