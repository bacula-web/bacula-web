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

namespace Core\Db;

use Doctrine\Persistence\AbstractManagerRegistry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ManagerRegistry extends AbstractManagerRegistry
{
    private ContainerInterface $container;
    public function __construct(
        string $name,
        array $connections,
        array $managers,
        string $defaultConnection,
        string $defaultManager,
        string $proxyInterfaceName,
        ContainerInterface $container
    ) {
        parent::__construct($name, $connections, $managers, $defaultConnection, $defaultManager, $proxyInterfaceName);

        $this->container = $container;
    }

    /**
     * @param string $name
     * @return mixed|object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getService(string $name): mixed
    {
        return $this->container->get($name);
    }

    /**
     * @param string $name
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function resetService(string $name): void
    {
        if ($this->getService($name) !== null) {
            $this->resetService($name);
        }
    }
}
