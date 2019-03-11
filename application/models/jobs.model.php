<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2019, Davide Franco			                            |
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
   // Parameters: $tablename = Job by default
   //             $filter (actualy unused, need to fix this
   // Return:		Number of clients
   // ==================================================================================

    public function count($tablename = 'Job', $filter = null)
    {
        return parent::count($tablename);
    }
    
    // ==================================================================================
    // Function: 	 count_Jobs()
    // Parameters: $period_timestamps		Array containing start and end date (unix timestamp format)
    //				 $job_status 			Job status (optional)
    //				 $job_level 				Job level (optional)
    // Return:		 Jobs count
    // ==================================================================================

    public function count_Jobs($period_timestamps, $job_status = null, $job_level = null)
    {
        $where        = null;
        $tablename    = 'Job';
        $fields        = array('COUNT(*) as job_count');
        
     // Check PDO object
        if (!is_a( $this->db_link, 'PDO' ) && is_null( $this->db_link) ) {
            throw new Exception('Unvalid PDO object provided in count_Jobs() function');
        }

        // Getting timestamp interval
        $intervals = CDBQuery::get_Timestamp_Interval($this->driver, $period_timestamps);
        
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
            } // end switch
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
                case 'completed with errors':
                    $where[] = "JobStatus IN ('E', 'e') ";
                   break;
                case 'failed':
                    $where[] = "JobStatus = 'f' ";
                    break;
                case 'canceled':
                    $where[] = "JobStatus = 'A' ";
                    break;
                case 'waiting':
                    $where[] = "JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
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
        $result = $this->run_query($statment);
        $result = $result->fetch();
        return $result['job_count'];
    }
    
    // ==================================================================================
    // Function: 	 getStoredFiles()
    // Parameters: $period_timestamps	Array containing start and end date (unix timestamp format)
    //				 $job_name			Job name (optional)
    //				 $client_id			Client id (optional)
    // Return:		 Total of stored files (backup) within the specific period
    // ==================================================================================

    public function getStoredFiles($period_timestamps = array(), $job_name = 'ALL', $client_id = 'ALL')
    {
        $where      = array();
        $fields     = array( 'SUM(JobFiles) AS stored_files' );
        $tablename    = 'Job';
        
     // Check PDO object
        if (!is_a($this->db_link, 'PDO') or is_null($this->db_link)) {
            throw new Exception('Unvalid PDO object provided in count_Jobs() function');
        }
        
     // Defined period
        $intervals     = CDBQuery::get_Timestamp_Interval($this->driver, $period_timestamps);
        $where[]     = '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
        
        if ($job_name != 'ALL') {
            $where[] = "name = '$job_name'";
        }
        
        if ($client_id != 'ALL') {
            $where[] = "clientid = '$client_id'";
        }
        // Get stored files only for Bacula job type <Backup>
        $where[] = "Type = 'B'";
        
     // Building SQL statment
        $statment = array( 'table' => $tablename, 'fields' => $fields, 'where' => $where);
        $statment = CDBQuery::get_Select($statment);

     // Execute query
        $result = $this->run_query($statment);
        $result = $result->fetch();
        
		// If result == null, return 0 instead
		if( is_null($result['stored_files']) ) {
			return 0;
		}else {
			return $result['stored_files'];
		}
    }

    // ==================================================================================
    // Function: 	 getStoredBytes()
    // Parameters: $period_timestamps 	Array containing start and end date (unix timestamp format)
    //				 $job_name			Job name (optional)
    //				 $client_id			Client id (optional)
    // Return:		 Total of stored bytes (backup) within the specific period
    // ==================================================================================

    public function getStoredBytes($period_timestamps = array(), $job_name = 'ALL', $client_id = 'ALL')
    {
        $where      = array();
        $fields     = array( 'SUM(JobBytes) AS stored_bytes' );
        $tablename    = 'Job';
        
        // Defined period
        $intervals     = CDBQuery::get_Timestamp_Interval($this->driver, $period_timestamps);
        $where[]     = '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
        
        if ($job_name != 'ALL') {
           $this->addParameter( 'jobname', $job_name);
           $where[] = "name = :jobname";
        }
        
        if ($client_id != 'ALL') {
            $where[] = "clientid = '$client_id'";
        }
        
        // Get stored files only for Bacula job type <Backup>
        $where[] = "Type = 'B'";

        // Building SQL statment
        $statment = array( 'table' => $tablename, 'fields' => $fields, 'where' => $where);
        $statment = CDBQuery::get_Select($statment);

        // Execute query
        $result = $this->run_query($statment);
        $result = $result->fetch();

		// If result == null, return 0 instead
		if( is_null($result['stored_bytes']) ) {
			return 0;
		}else {
			return $result['stored_bytes'];
		}
    }

    // ==================================================================================
    // Function: 	 count_Job_Names()
    // Parameters: none	
    // Return:		 total of defined jobs name
    // ==================================================================================

    public function count_Job_Names()
    {
        $fields        = array( 'COUNT(DISTINCT Name) AS job_name_count' );

     // Prepare and execute query
        $statment     = CDBQuery::get_Select(array( 'table' => 'Job', 'fields' => $fields ));
        $result     = $this->run_query($statment);

        $result        = $result->fetch();
        return $result['job_name_count'];
    }

    // ==================================================================================
    // Function: 	 get_Jobs_List()
    // Parameters: $client_id 	Client id (optional)
    //             $job_type     Job Type (optional)
    // Return:		 List of defined jobs name
    // ==================================================================================

    public function get_Jobs_List( $client_id = null, $job_type = null)
    {
        $jobs   = array();
        $fields = array( 'Name');
        $where  = null;

        // Prepare and execute query
        if (!is_null($client_id)) {
           $where[] = "clientid = '$client_id'";
        }

        // Job type filter
        if( !is_null( $job_type ) ) {
           $where[] = "type = '$job_type'";
        }

        $statment   = array( 'table' => 'Job', 'fields' => $fields, 'groupby' => 'Name', 'orderby' => 'Name', 'where' => $where );
        $result     = $this->run_query(CDBQuery::get_Select($statment));

        foreach ($result->fetchAll() as $job) {
            $jobs[] = $job['name'];
        }
        
        return $jobs;
    }

    // ==================================================================================
    // Function: 	 getLevels()
    // Parameters: $levels_name - Array containing level name
    // Return:		 array containing level list
    // ==================================================================================

    public function getLevels($levels_name = array())
    {
        $levels = array();
        $statment = array( 'table' => 'Job', 'fields' => array('Level'), 'groupby' => 'Level');
        $result = $this->run_query(CDBQuery::get_Select($statment));

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

    // ==================================================================================
    // Function: 	   getUsedJobTypes()
    // Parameters:   array of available Bacula job types 
    // Return:		   array containing distinct list of jobs type
    // ==================================================================================

    public function getUsedJobTypes( $job_types )
    {
        $used_types = array();
        $sql_query = "SELECT DISTINCT Type from Job";
        $result = $this->run_query($sql_query);

        foreach ($result->fetchAll() as $job_type) {
           if( array_key_exists( $job_type['type'], $job_types) ) {
              $used_types[ $job_type['type'] ] = $job_types[ $job_type['type']];
           }
        }

        return $used_types;
    }

    // ==================================================================================
    // Function: 	   getWeeklyJobsStats()
    // Parameters:   none 
    // Return:		   array containing stored bytes and files of completed backup jobs for each day of the week
    // ==================================================================================

    public function getWeeklyJobsStats() 
    {
       $fields = array( 'SUM(Job.Jobbytes) as jobbytes' , 'SUM(Job.Jobfiles) as jobfiles');
       $where = array("Job.JobStatus = 'T'", "Job.Type = 'B'");
       $orderby = 'JobBytes DESC';
       $groupby = 'dayofweek';
       $res = array();

       switch($this->driver) {
       case 'mysql':
          $fields[] = "FROM_UNIXTIME(Job.JobTDate, '%W') AS dayofweek";
          break;
       case 'pgsql':
          $fields[] = 'extract(dow from Job.EndTime::timestamp) AS dayofweek';
          break;
       case 'sqlite':
          return null;
       } // end switch

       $query = CDBQuery::get_Select( array( 'table' => 'Job',
          'fields' => $fields,
          'where' => $where,
          'groupby' => $groupby,
          'orderby' => $orderby));

       $result = $this->run_query($query);

       $week = array( 0 => 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

       foreach( $result->fetchAll() as $day ) {
          $day['jobbytes'] = CUtils::Get_Human_Size($day['jobbytes']);
          $day['jobfiles'] = CUtils::format_Number($day['jobfiles']);
          
          // Simply fix day name for postgreSQL
          // It could be improved but I lack some SQL (postgreSQL skills)
          if( $this->driver == 'pgsql' ) {
             $day['dayofweek'] = $week[ $day['dayofweek'] ];
          }
          
          $res[] = $day;
       } 

       return $res;
    }

    // ==================================================================================
    // Function: 	   getBiggestJobsStats()
    // Parameters:   none 
    // Return:		   array containing 10 biggest backup jobs (stored bytes) 
    // ==================================================================================

    public function getBiggestJobsStats() 
    {
       $fields = array( 'Job.Jobbytes', 'Job.Jobfiles', 'Job.Name');
       $where = array("Job.JobStatus = 'T'", "Job.Type = 'B'");
       $res = array();

       $query = CDBQuery::get_Select( array( 'table' => 'Job',
          'fields' => $fields,
          'where' => $where,
          'orderby' => 'jobbytes DESC',
          'limit' => '10'));

       $result = $this->run_query($query);

       foreach( $result->fetchAll() as $job) {
          $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);
          $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);
          $res[] = $job;
       } 
      
       return $res;
    }
}
