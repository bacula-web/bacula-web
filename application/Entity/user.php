<?php

namespace App\Entity;

class User {

    private $id;

    private $username;

    private $email;

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }
}