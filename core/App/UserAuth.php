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

namespace Core\App;

use App\Tables\UserTable;
use Core\Db\DatabaseFactory;
use Core\Exception\AppException;
use Core\Exception\DatabaseException;
use Exception;
use PDO;
use Symfony\Component\HttpFoundation\Session\Session;

class UserAuth
{
    /**
     * @var string
     */
    protected string $tablename = 'Users';

    /**
     * @var string
     */
    protected string $appDbBackend = BW_ROOT . '/application/assets/protected/application.db';

    /**
     * @var UserTable
     */
    private UserTable $userTable;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->userTable = new UserTable(
            DatabaseFactory::getDatabase()
        );
    }

    /**
     * @throws AppException
     */
    public function check()
    {
        // Throw an exception if PHP SQLite is not installed
        $pdo_drivers = PDO::getAvailableDrivers();

        if (! in_array($this->userTable->get_driver_name(), $pdo_drivers)) {
            throw new DatabaseException('PHP SQLite support not found');
        }

        // Check protected assets folder permissions$
        $webUser = array();
        exec('whoami', $webUser);
        $webUser = reset($webUser);

        $assetsOwner = posix_getpwuid(fileowner($this->appDbBackend));

        if ($webUser != $assetsOwner['name']) {
            throw new AppException('Bad ownership / permissions for protected assets folder (application/assets/protected)');
        }
    }

    /**
     * @return void
     * @throws AppException
     */
    public function checkSchema(): void
    {
        // Check if sqlite db file is writable
        if (!is_writable($this->appDbBackend)) {
            throw new DatabaseException('Application backend database file is not writable, please fix it');
        }

        // Check if Users table exist
        $query = "SELECT name FROM sqlite_master WHERE type='table' AND name='Users';";

        $res = $this->userTable->run_query($query);
        $res = $res->fetchAll();

        // Users table do not exist, let's create it
        if (count($res) == 0) {
            # If Users table not found, raise an exception
            throw new AppException('Users authentication database not found, 
              have a look at the chapter <b>Installation / Finalize your setup</b> in the <a href="https://docs.bacula-web.org" target="_blank">documentation</a>');
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    public function authUser(string $username, string $password): string
    {
        // TODO: FILTER_SANITIZE_STRING is deprecated as of PHP 8.1, replace by htmlspecialchars() instead

        // Sanitize username
        $username = filter_var($username, FILTER_SANITIZE_STRING, array( 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
        $username = trim($username, ' ');

        $user = $this->userTable->findByName($username);

        if ($user) {
            if (password_verify($password, $user->getPasswordHash())) {
                return 'yes';
            } else {
                return 'no';
            }
        } else {
            // TODO: Display a flash message like "User not found or password incorrect"
            return 'no';
        }
    }

    /**
     * @return void
     */
    public function destroySession()
    {
        $session = new Session();
        $session->clear();
        $session->invalidate();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie($session->getName(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
    }

    /**
     * @return bool
     */
    public function authenticated(): bool
    {
        $session = new Session();
        if ($session->get('user_authenticated') === 'yes') {
            return true;
        }

        return false;
    }
}
