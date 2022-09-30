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
            $sqlQuery = 'SELECT * from Users WHERE username = :username';
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
            'hashedPassword' => $user->getHashedPassword()
        ];

        $query = 'UPDATE ' . $this->tablename . ' SET passwordHash = :hashedPassword WHERE username = :username';
        return $this->update($query, $parameters);
    }
}
