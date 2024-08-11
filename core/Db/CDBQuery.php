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

use DateTimeImmutable;
use Exception;
use TypeError;

class CDBQuery
{
    /**
     * get_Select
     *
     * @param array<string,mixed> $param array which could contain key listed below
     *  $param = [
     *      'table'     => (string) name of the table for FROM
     *      'fields'    => (array) array of fields
     *      'where'     => (array) array of key/pair conditions (separated by AND if several)
     *      'join'      => (array) array ['table' => table name to LEFT JOIN, 'condition' => JOIN condition]
     *      'groupby'   => (string) add GROUP BY field
     *      'orderby'   => (string) add ORDER BY field
     *      'limit'     => (array) array ['count' => define the limit, 'offset' => define the OFFSET]
     *
     * @param string|null $driver
     * @return string computed SQL query
     */
    public static function get_Select(array $param, string $driver = null): string
    {
        $query = 'SELECT ';
        $where = '';

        if (empty($param)) {
            throw new TypeError('Missing parameters: you should provide an array');
        }

        // Buidling SQL query
        // Fields
        if (isset($param['fields'])) {
            foreach ($param['fields'] as $field) {
                $query .= $field;
                if (end($param['fields']) != $field) {
                    $query .= ', ';
                } else {
                    $query .= ' ';
                }
            }
        } else {
            $query .= '* ';
        }

        // From
        $query .= 'FROM ' . $param['table'] . ' ';

        // Join
        if (isset($param['join']) && is_array($param['join'])) {
            foreach ($param['join'] as $join) {
                $query .= 'LEFT JOIN ' . $join['table'] . ' ON ' . $join['condition'] . ' ';
            }
        }

        // Where
        if (isset($param['where']) && is_array($param['where'])) {
            foreach ($param['where'] as $key => $where_item) {
                if ($key > 0) {
                    $where .= "AND $where_item ";
                } else {
                    $where .= "$where_item ";
                }
            }

            $query .= 'WHERE ' . $where . ' ';
        }

        // Group by
        if (isset($param['groupby'])) {
            $query .= 'GROUP BY ' . $param['groupby'] . ' ';
        }

        // Order by
        if (isset($param['orderby'])) {
            $query .= 'ORDER BY ' . $param['orderby'] . ' ';
        }

        // Limit
        if (isset($param['limit'])) {
            $limit = $param['limit'];

            // we passed an array( 'count' => $count, 'offset' => $offset)
            if (is_array($limit)) {
                if (($driver == 'pgsql') || ($driver == 'mysql')) {
                    // postgreSQL query
                    $query .= 'LIMIT ' . $limit['count'] . ' OFFSET ' . $limit['offset'];
                } else {
                    // MySQL query
                    $query .= 'LIMIT ' . $limit['offset'] . ',' . $limit['count'];
                }
            }

            // we passed limit as an integer
            if (is_numeric($limit)) {
                $query .= 'LIMIT ' . $param['limit'];
            }
        }

        return $query;
    }

    /**
     * @param string $driver
     * @param array<int,int> $period_timestamp
     * @return array<string,string>
     * @throws Exception
     */
    public static function get_Timestamp_Interval(string $driver, array $period_timestamp = []): array
    {
        $period = [];
        $dateformat = 'Y-m-d H:i:s';

        $start = new DateTimeImmutable('@'.strval($period_timestamp[0]));
        $formattedStartTime = $start->format($dateformat);

        $end = new DateTimeImmutable('@'.strval($period_timestamp[1]));
        $formattedEndTime = $end->format($dateformat);

        if ($driver === 'pgsql') {
            $period['starttime'] = "TIMESTAMP '" . $formattedStartTime . "'";
            $period['endtime'] = "TIMESTAMP '" . $formattedEndTime . "'";
        } else {
            $period['starttime'] = "'" . $formattedStartTime . "'";
            $period['endtime'] = "'" . $formattedEndTime . "'";
        }

        return $period;
    }
}
