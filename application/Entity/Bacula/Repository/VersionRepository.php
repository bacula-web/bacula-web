<?php

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of the Bacula-Web project.
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

use App\Entity\Bacula\Version;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

/**
 * @method Version|null find($id, $lockMode = null, $lockVersion = null)
 * @method Version|null findOneBy(array $criteria, array $orderBy = null)
 * @method Version[] findAll()
 * @method Version[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersionRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Version::class);
    }

    /**
     * Return Bacula catalog version
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCatalogVersion(): int
    {
        $queryBuilder = $this->createQueryBuilder('v');
        return $queryBuilder
            ->select('v')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return current timestamp from database server as Carbon instance
     *
     * @return Carbon
     */
    public function getCurrentDateTime(): Carbon
    {
        $sql = 'SELECT j.id, CURRENT_TIMESTAMP() as current_timestamp FROM App\Entity\Bacula\Version j';
        $result = $this
            ->getEntityManager()
            ->createQuery($sql)
            ->getArrayResult();

        return Carbon::create($result[0]['current_timestamp']);
    }

    /**
     * Get Bacula directory catalog database size
     *
     * @return float
     * @throws Exception
     */
    public function getDatabaseSize(): float
    {
        $size = 0;
        $connection = $this->getEntityManager()->getConnection();
        $dbName = $connection->getDatabase();

        $platform = $connection->getDriver()->getDatabasePlatform();

        /**
         * @var PDO $pdo
         */
        $pdo = $connection->getNativeConnection();

        switch (get_class($platform)) {
            case 'Doctrine\DBAL\Platforms\MySQLPlatform':
            case 'Doctrine\DBAL\Platforms\MySQL57Platform':
            case 'Doctrine\DBAL\Platforms\MySQL80Platform':
            case 'Doctrine\DBAL\Platforms\MariaDbPlatform':
            case 'Doctrine\DBAL\Platforms\MariaDb1043Platform':
            case 'Doctrine\DBAL\Platforms\MariaDb1052Platform':
            case 'Doctrine\DBAL\Platforms\MariaDb1060Platform':
                $sqlQuery = "SELECT table_schema AS 'database', 
                             (sum( data_length + index_length) / 1024 / 1024 ) AS 'dbsize' 
                             FROM information_schema.TABLES
                             WHERE table_schema = '$dbName'
                             GROUP BY table_schema";

                $statement = $pdo->prepare($sqlQuery);
                $result = $statement->execute();

                if ($result) {
                    $size = $statement->fetch()['dbsize'];
                }

                break;
            case 'Doctrine\DBAL\Platforms\PostgreSqlPlatform':
            case 'Doctrine\DBAL\Platforms\PostgreSQL94Platform':
            case 'Doctrine\DBAL\Platforms\PostgreSQL100Platform':
                $sqlQuery = "SELECT pg_database_size('$dbName') AS dbsize";

                $statement = $pdo->prepare($sqlQuery);
                $result = $statement->execute();

                if ($result) {
                    $size = $statement->fetch()['dbsize'];
                }
                break;
            default:
                /**
                 * TODO: throw an error or exception here
                 */
                dd('Unsupported database platform');
        }

        return (float) $size;
    }
}
