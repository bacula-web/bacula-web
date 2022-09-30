<?php

namespace App\Tables;

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
            return $this->query( $sqlQuery,
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

        return $this->query($getUsersQuery, null, '\App\Entity\User');
    }
}
