<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2018, Davide Franco	                                    |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
 */

class UserAuth extends CModel { 

    protected $appDbBackend;
    protected $dsn;

    public function __construct() {

        $this->cdb  = new CDB();

        $this->appDbBackend = 'application/assets/protected/application.db';
        $this->dsn = "sqlite:$this->appDbBackend";

        $this->db_link = $this->cdb->connect($this->dsn);
    }

    public function checkSchema() {
        
        // Check if sqlite db file is writable
        if(!is_writable($this->appDbBackend)) {
            throw new Exception('Application backend database file is not writable, please fix it');
        }

        // Check if Users table exist
        $query = "SELECT name FROM sqlite_master WHERE type='table' AND name='Users';";
        $res = $this->run_query($query);

        $res = $res->fetchAll();

        // Users table do not exist, let's create it
        if( count($res) == 0) {
            $this->createSchema();
            
            // Create default user
            $this->addUser( 'admin', 'bacula');
        }

    }

    public function createSchema() {

        $createSchemaQuery = 'CREATE TABLE IF NOT EXISTS Users (
                        user_id INTEGER PRIMARY KEY,
                        username TEXT NOT NULL UNIQUE,
                        passwordHash TEXT NOT NULL,
                        email TEXT
                        );
                        CREATE INDEX IF NOT EXISTS User_ix_username ON Users (username);';

        $this->run_query($createSchemaQuery);
    }

    protected function addUser($username, $password) {
        
        $hashedPassword = password_hash( $password, CRYPT_BLOWFISH);
        $addUserQuery = "INSERT INTO Users (username,passwordHash) VALUES ('$username','$hashedPassword');";
        $this->run_query($addUserQuery);
    }

    public function authUser( $username, $password) {
        
        $authUserQuery = "SELECT passwordHash FROM Users WHERE ";
        $authUserQuery .= "username = :username LIMIT 1";
        $this->addParameter( 'username', $username);

        $result = $this->run_query($authUserQuery);
        $result = $result->fetchAll();

        if(count($result) == 0) {
            echo "<pre>username or password incorrect</pre>";
        }else{
            if( password_verify($password, $result[0]['passwordhash']) == TRUE) {
                return 'yes';
            }else{
                return 'no';
            }
        }
    }

    public function getData( $username) {

        $getUserDataQuery = "SELECT username,email FROM Users WHERE username = :username LIMIT 1";
        $this->addParameter( 'username', $username);

        $result = $this->run_query($getUserDataQuery);
        $result = $result->fetchAll();
        
        return $result[0];
    }

    public function setPassword( $username, $password) {

        $hashedPassword = password_hash( $password, CRYPT_BLOWFISH);
        $updateUserQuery = "UPDATE Users SET passwordHash = '$hashedPassword' WHERE username = :username;";
        echo $updateUserQuery;
        $this->addParameter( 'username', $username);
        $this->run_query($updateUserQuery);
    }

    public function destroySession() {
        
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
        }
        
        // Destroy the session.
        session_destroy();
    }

} // end of class
