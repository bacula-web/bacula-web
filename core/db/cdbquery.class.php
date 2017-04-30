<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco                                      |
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


class CDBQuery
{
 
    // ==================================================================================
    // Function: 	get_Select()
    // Parameters: 	array containing all informations needed to build the SQL statment
    // Return:		SELECT SQL statment
    // ==================================================================================
    public static function get_Select($param = array())
    {
        $query = 'SELECT ';
        $where = '';

        if (!is_array($param) || empty($param)) {
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
            $query .= 'LEFT JOIN ' . $param['join']['table'] . ' ON ' . $param['join']['condition'] . ' ';
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

        // Limit to
        if (isset($param['limit']) && !is_null($param['limit'])) {
            $query .= 'LIMIT ' . $param['limit'];
        }

        return $query;
    }
    
  // ==================================================================================
    // Function: 	get_Timestamp_Interval()
    // Parameters:	$period array containing start and end timestamp
    // Return:		return table with correct case
    // ==================================================================================
    public static function get_Timestamp_Interval($period_timestamp = array())
    {
        $period = array();
        
        switch (CDB::getDriverName()) {
            case 'pgsql':
                $period['starttime']     = "TIMESTAMP '" . date("Y-m-d H:i:s", $period_timestamp[0]) . "'";
                $period['endtime']       = "TIMESTAMP '" . date("Y-m-d H:i:s", $period_timestamp[1]) . "'";
                break;
            default:
                $period['starttime']     = "'" . date("Y-m-d H:i:s", $period_timestamp[0]) . "'";
                $period['endtime']       = "'" . date("Y-m-d H:i:s", $period_timestamp[1]) . "'";
                break;
        } // end switch

        return $period;
    }
}
