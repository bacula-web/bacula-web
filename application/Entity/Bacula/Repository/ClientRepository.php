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

namespace App\Entity\Bacula\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * This is the Doctrine Repository class for the Client entity.
 */
class ClientRepository extends EntityRepository
{
    /**
     * Return an array of clients (optionally only active clients)
     *
     * @param bool $showInactiveClient
     * @return array
     */
    public function getClients(bool $showInactiveClient = false): array
    {
        $queryBuilder = $this
            ->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.name', 'ASC');

        if (!$showInactiveClient) {
            $queryBuilder
                ->andWhere('c.fileRetention > 0')
                ->andWhere('c.jobRetention > 0');
        }

        return $queryBuilder
            ->getQuery()
            ->getArrayResult();
    }
}
