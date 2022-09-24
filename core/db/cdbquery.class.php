<?php

/**
 * Copyright (C) 2010-2022 Davide Franco
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

class CDBQuery
{
    /**
     * get_Select
     *
     * @param array $param array which could contain key listed below
     *  $param = [
     *      'table'     => (string) name of the table for FROM
     *      'fields'    => (array) array of fields
     *      'where'     => (array) array of key/pair conditions (separated by AND if several)
     *      'join'      => (array) array ['table' => table name to LEFT JOIN, 'condition' => JOIN condition]
     *      'groupby'   => (string) add GROUP BY field
     *      'orderby'   => (string) add ORDER BY field
     *      'limit'     => (array) array ['count' => define the limit, 'offset' => define the OFFSET]
     *
     * @param string $driver
     * @return string computed SQL query
     * @throws Exception
     */

    public static function get_Select(array $param, $driver = null): string
    {
        $query = 'SELECT ';
        $where = '';

        if (empty($param)) {
            throw new Exception("Missing parameters: you should provide an array");
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
        if (isset($param['join']) && !is_null($param['join']) && is_array($param['join'])) {
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
            } // end foreach

            $query .= 'WHERE ' . $where . ' ';
        }
        
        // Group by
        if (isset($param['groupby']) && !is_null($param['groupby'])) {
            $query .= 'GROUP BY ' . $param['groupby'] . ' ';
        }
            
        // Order by
        if (isset($param['orderby']) && !is_null($param['orderby'])) {
            $query .= 'ORDER BY ' . $param['orderby'] . ' ';
        }

        // Limit
        if (isset($param['limit']) && !is_null($param['limit'])) {
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
    
    // ==================================================================================
    // Function: 	get_Timestamp_Interval()
    // Parameters:	$period array containing start and end timestamp
    // Return:		return table with correct case
    // ==================================================================================
    public static function get_Timestamp_Interval($driver, $period_timestamp = array())
    {
        $period = array();
        
        switch ($driver) {
            case 'pgsql':
                $period['starttime']     = "TIMESTAMP '" . date("Y-m-d H:i:s", $period_timestamp[0]) . "'";
                $period['endtime']       = "TIMESTAMP '" . date("Y-m-d H:i:s", $period_timestamp[1]) . "'";
                break;
            default:
                $period['starttime']     = "'" . date("Y-m-d H:i:s", $period_timestamp[0]) . "'";
                $period['endtime']       = "'" . date("Y-m-d H:i:s", $period_timestamp[1]) . "'";
        } // end switch

        return $period;
    }
}
