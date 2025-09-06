<?php

/**
 * Copyright (C) 2010-present Davide Franco
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

declare(strict_types=1);

namespace Core\App;

use App\Entity\Core\User;
use App\Table\UserTable;
use Core\Db\ManagerRegistry;
use Core\Exception\AppException;
use Core\Exception\DatabaseException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Odan\Session;
use Odan\Session\SessionInterface;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

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
    private SessionInterface $session;

    /**
     * @var EntityManager
     */
    private EntityManager $em;

    /**
     * @param UserTable $userTable
     * @param SessionInterface $session
     * @param ContainerInterface $container
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(UserTable $userTable, SessionInterface $session, ManagerRegistry $managerRegistry)
    {
        $this->userTable = $userTable;
        $this->session = $session;
        $this->em = $managerRegistry->getManager();
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
        $webUser = [];
        exec('whoami', $webUser);
        $webUser = reset($webUser);

        $assetsOwner = posix_getpwuid(fileowner($this->appDbBackend));

        if ($assetsOwner === false) {
            // TODO: this condition should be handled
        } elseif ($webUser !== $assetsOwner['name']) {
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
              have a look at the chapter <b>Installation / Finalize your setup</b> in the <a href="https://www.bacula-web.org/docs/install/setup-user-auth" target="_blank">documentation</a>');
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @return string
     * @throws NonUniqueResultException
     */
    public function authUser(string $username, string $password): string
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $user = $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();

        if ($user) {
            if (password_verify($password, $user->getPassword())) {
                return 'yes';
            } else {
                return 'no';
            }
        }

        return 'no';
    }

    /**
     * @param SessionInterface $session
     * @return void
     */
    public function destroySession(Session\SessionInterface $session): void
    {
        $session->destroy();
        // TODO: starting the session is not required as this task is already handled by the SessionStart middleware
        $session->start();
        $session->regenerateId();
    }

    /**
     * @return bool
     */
    public function authenticated(): bool
    {
        if ($this->session->get('user_authenticated') === 'yes') {
            return true;
        }

        return false;
    }
}
