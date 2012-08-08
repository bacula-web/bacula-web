<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2012, Davide Franco                                              |
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

class CDBQuery {

    public static function getQuery($param) {
        $query = '';

        if (!is_array($param) || empty($param)) {
            throw new Exception("Missing parameters: you should provide an array");
            exit;
        }

        // Buidling SQL query
        $query = 'SELECT ';

        // Fields
        if (isset($param['fields'])) {
            foreach ($param['fields'] as $field) {
                $query .= $field . ' ';
                if (end($param['fields']) != $field)
                    $query .= ', ';
            }
        }else {
            $query .= '* ';
        }

        // From
        $query .= 'FROM ' . $param['table'] . ' ';

        // Join
        if (isset($param['join']) && !is_null($param['join']) && is_array($param['join']))
            $query .= 'LEFT JOIN ' . $param['join']['table'] . ' ON ' . $param['join']['condition'] . ' ';

        // Where
        if (isset($param['where']) && !is_null($param['where']))
            $query .= 'WHERE ' . $param['where'] . ' ';

        // Order by
        if (isset($param['orderby']) && !is_null($param['orderby']))
            $query .= 'ORDER BY ' . $param['orderby'] . ' ';

        // Group by
        if (isset($param['groupby']) && !is_null($param['groupby']))
            $query .= 'GROUP BY ' . $param['groupby'] . ' ';

        // Limit to
        if (isset($param['limit']) && !is_null($param['limit']))
            $query .= 'LIMIT ' . $param['limit'];

        return $query;
    }

}

?>
