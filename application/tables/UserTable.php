<?php

namespace App\Tables;

use App\Entity\User;
use Core\App\CErrorHandler;
use Core\Db\Table;

class UserTable extends Table
{
    protected $tablename = 'Users';

    /**
     * @param $username
     * @return mixed
     */
    public function findByName($username)
    {
        try {
            $sqlQuery = "SELECT * FROM 'Users' WHERE username = :username";

            return $this->select( $sqlQuery,
                ['username' => $username],
                '\App\Entity\User',
                true
            );
        } catch ( \PDOException $e) {
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
     * @return bool
     */
    public function setPassword(string $username, string $password): bool
    {
        $user = $this->findByName($username);

        $user->setPassword($password);

        $parameters = [
            'username' => $user->getUsername(),
            'hashedPassword' => $user->getPasswordHash()
        ];

        $query = 'UPDATE ' . $this->tablename . ' SET passwordHash = :hashedPassword WHERE username = :username';

        return $this->update($query, $parameters);

        /*
        $result = $this->execute($query, $parameters);
        if ($result !== false) {
            var_dump($result);
            die();
        }
        */
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

        return $this->create($addUserQuery, $parameters);
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
