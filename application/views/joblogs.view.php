<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2021, Davide Franco			                            |
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

class JobLogsView extends CView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->templateName = 'joblogs.tpl';
        $this->name = 'Job logs';
        $this->title = 'Bacula job log';
    }

    public function prepare()
    {
        $joblogs = array();
        $jobid = CHttpRequest::get_Value('jobid');
    
        // If $_GET['jobid'] is null and is not a number, throw an Exception
        if (is_null($jobid) or !is_numeric($jobid)) {
            throw new Exception('Invalid job id (invalid or null) provided in Job logs report');
        }

        // Prepare and execute SQL statment
        $jobs = new Jobs_Model();
        $statment     = array('table' => 'Log', 'where' => array("JobId = :jobid"), 'orderby' => 'Time');
        $jobs->addParameter('jobid', $jobid);
        $result     = $jobs->run_query(CDBQuery::get_Select($statment),$jobs->get_driver_name());

        // Processing result
        foreach ($result->fetchAll() as $log) {
            $log['logtext'] = nl2br($log['logtext']);
            $log['time'] = date($_SESSION['datetime_format'], strtotime($log['time']));
            $joblogs[] = $log;
        }
        
        $this->assign('jobid', $jobid);
        $this->assign('joblogs', $joblogs);
    } // end of prepare() method
} // end of class
