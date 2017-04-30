<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco			                               |
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

class Jobs_Model extends CModel
{
    
    // ==================================================================================
    // Function: 	count()
    // Parameters:	$pdo_connection			Valid PDO connection object
    // Return:		Number of clients
    // ==================================================================================

    public static function count($pdo, $tablename = 'Job', $filter = null)
    {
        return CModel::count($pdo, $tablename);
    }
    
    // ==================================================================================
    // Function: 	count_Jobs()
    // Parameters:	$pdo_connection			Valid PDO connection object
    //				$period_timestamps		Array containing start and end date (unix timestamp format)
    //				$job_status 			Job status (optional)
    //				$job_level 				Job level (optional)
    // Return:		Jobs count
    // ==================================================================================

    public static function count_Jobs($pdo_connection, $period_timestamps, $job_status = null, $job_level = null)
    {
        $where        = null;
        $tablename    = 'Job';
        $fields        = array('COUNT(*) as job_count');
        
     // Check PDO object
        if (!is_a($pdo_connection, 'PDO') and is_null($pdo_connection)) {
            throw new Exception('Unvalid PDO object provided in count_Jobs() function');
        }

     // PDO object singleton
        if (is_null(CModel::$pdo_connection)) {
            CModel::$pdo_connection = $pdo_connection;
        }
        
     // Getting timestamp interval
        $intervals  = CDBQuery::get_Timestamp_Interval($period_timestamps);
        
     // Defining interval depending on job status
        if (!is_null($job_status)) {
            switch ($job_status) {
                // Using Bacula version 5.0.3, waiting jobs have both starttime and endtime set 0000-00-00 00:00:00
                // Running set to YYYY-mm-dd hh:mm:ss (replace by real time) and endtime set to 0000-00-00 00:00:00
                // So, I'd not use starttime and endtime for waiting and running jobs here
                case 'waiting':
                case 'running':
                    break;
                default:
                    $where = array( '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ' );
                    break;
            }
        } else {
            $where[] = '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
        }
        
     // Job status
        if (!is_null($job_status)) {
            switch ($job_status) {
                case 'running':
                    $where[] = "JobStatus = 'R'" ;
                    break;
                case 'completed':
                    $where[] = "JobStatus = 'T' ";
                    break;
                case 'failed':
                    $where[] = "JobStatus IN ('f','E') ";
                    break;
                case 'canceled':
                    $where[] = "JobStatus = 'A' ";
                    break;
                case 'waiting':
                    $where[] = "JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
                    break;
            } // end switch
        }
        
     // Job level
        if (!is_null($job_level)) {
            $where[] = "Level = '$job_level' ";
        }
        
     // Building SQL statment
        $statment = array( 'table' => $tablename, 'fields' => $fields, 'where' => $where);
        $statment = CDBQuery::get_Select($statment);
        
     // Execute SQL statment
        $result = CDBUtils::runQuery($statment, $pdo_connection);
        $result = $result->fetch();
        return $result['job_count'];
    }
    
    // ==================================================================================
    // Function: 	getStoredFiles()
    // Parameters:	$pdo_connection 	Valid pdo connection object
    //				$period_timestamps	Array containing start and end date (unix timestamp format)
    //				$job_name			Job name (optional)
    //				$client_id			Client id (optional)
    // Return:		Total of stored files within the specific period
    // ==================================================================================

    public static function getStoredFiles($pdo_connection, $period_timestamps = array(), $job_name = 'ALL', $client_id = 'ALL')
    {
        $where      = array();
        $fields     = array( 'SUM(JobFiles) AS stored_files' );
        $tablename    = 'Job';
        
     // Check PDO object
        if (!is_a($pdo_connection, 'PDO') or is_null($pdo_connection)) {
            throw new Exception('Unvalid PDO object provided in count_Jobs() function');
        }
        
     // Defined period
        $intervals     = CDBQuery::get_Timestamp_Interval($period_timestamps);
        $where[]     = '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
        
        if ($job_name != 'ALL') {
            $where[] = "name = '$job_name'";
        }
        
        if ($client_id != 'ALL') {
            $where[] = "clientid = '$client_id'";
        }
        
     // Building SQL statment
        $statment = array( 'table' => $tablename, 'fields' => $fields, 'where' => $where);
        $statment = CDBQuery::get_Select($statment);

     // Execute query
        $result = CDBUtils::runQuery($statment, $pdo_connection);
        $result = $result->fetch();
        
		// If result == null, return 0 instead
		if( is_null($result['stored_files']) ) {
			return 0;
		}else {
			return $result['stored_files'];
		}
    }

    // ==================================================================================
    // Function: 	getStoredBytes()
    // Parameters:	$pdo_connection		Valid PDO connection object
    //				$period_timestamps 	Array containing start and end date (unix timestamp format)
    //				$job_name			Job name (optional)
    //				$client_id			Client id (optional)
    // Return:		Total of stored bytes within the specific period
    // ==================================================================================

    public static function getStoredBytes($pdo_connection, $period_timestamps = array(), $job_name = 'ALL', $client_id = 'ALL')
    {
        $where      = array();
        $fields     = array( 'SUM(JobBytes) AS stored_bytes' );
        $tablename    = 'Job';
        
        // Defined period
        $intervals     = CDBQuery::get_Timestamp_Interval($period_timestamps);
        $where[]     = '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
        
        if ($job_name != 'ALL') {
            $where[] = "name = '$job_name'";
        }
        
        if ($client_id != 'ALL') {
            $where[] = "clientid = '$client_id'";
        }
        
        // Building SQL statment
        $statment = array( 'table' => $tablename, 'fields' => $fields, 'where' => $where);
        $statment = CDBQuery::get_Select($statment);

        // Execute query
        $result = CDBUtils::runQuery($statment, $pdo_connection);
        $result = $result->fetch();

		// If result == null, return 0 instead
		if( is_null($result['stored_bytes']) ) {
			return 0;
		}else {
			return $result['stored_bytes'];
		}
    }

    // ==================================================================================
    // Function: 	count_Job_Names()
    // Parameters:	$pdo 	Valid PDO connection object
    // Return:		total of defined jobs name
    // ==================================================================================

    public static function count_Job_Names($pdo)
    {
        $fields        = array( 'COUNT(DISTINCT Name) AS job_name_count' );

     // Prepare and execute query
        $statment     = CDBQuery::get_Select(array( 'table' => 'Job', 'fields' => $fields ));
        $result     = CDBUtils::runQuery($statment, $pdo);

        $result        = $result->fetch();
        return $result['job_name_count'];
    }

    // ==================================================================================
    // Function: 	get_Jobs_List()
    // Parameters:	$pdo 		Valid PDO connection object
    //				$client_id 	Client id (optinoal)
    // Return:		Total of defined jobs name
    // ==================================================================================

    public static function get_Jobs_List($pdo, $client_id = null)
    {
        $jobs   = array();
        $fields = array( 'Name' );
        $where  = null;

     // Prepare and execute query
        if (!is_null($client_id)) {
            $where[] = "clientid = '$client_id'";
        }

        $statment   = array( 'table' => 'Job', 'fields' => $fields, 'groupby' => 'Name', 'orderby' => 'Name', 'where' => $where );
        $result     = CDBUtils::runQuery(CDBQuery::get_Select($statment), $pdo);

        foreach ($result->fetchAll() as $job) {
            $jobs[] = $job['name'];
        }
        
        return $jobs;
    }

    // ==================================================================================
    // Function: 	getLevels()
    // Parameters:	$pdo_connection - valid PDO object
    //                  $levels_name - Array containing level name
    // Return:		array containing level list
    // ==================================================================================

    public static function getLevels($pdo, $levels_name = array())
    {
        $levels = array();
        $statment = array( 'table' => 'Job', 'fields' => array('Level'), 'groupby' => 'Level');
        $result = CDBUtils::runQuery(CDBQuery::get_Select($statment), $pdo);

        foreach ($result->fetchAll() as $level) {
            if (array_key_exists($level['level'], $levels_name)) {
                $levels[$level['level']] = $levels_name[$level['level']];
            }
            else {
                $levels[$level['level']] = $level['level'];
            }
        }

        return $levels;
    }
}
