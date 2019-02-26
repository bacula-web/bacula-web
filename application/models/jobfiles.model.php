<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of historyfiles
 *
 * @author gorlando
 */
class JobFiles_Model extends CModel {
	
	public function getJobFiles($jobId, $limit, $offset){
		$used_types = array();
        $sql_query = "SELECT `Job`.`Name`, `Job`.`JobStatus`, `File`.`FileIndex`, CONCAT(`Path`.`Path`, `Filename`.`Name`) as Path 
			FROM `File`, `Path`, `Filename`, `Job` 
			WHERE `File`.`JobId` = $jobId 
			AND  `Path`.`PathId` = `File`.`PathId` 
			AND  `Filename`.`FilenameId` = `File`.`FilenameId` 
			AND  `Job`.`JobId` = `File`.`JobId` 
			ORDER BY `File`.`FileIndex` ASC
            LIMIT ".($offset*$limit).",$limit ;";

        $result = $this->run_query($sql_query);

        $used_types = $result->fetchAll();

        return $used_types;
	}
	
	public function countJobFiles($jobId){
		$used_types = array();
        $sql_query = "SELECT COUNT(*) as count
			FROM `File`, `Path`, `Filename`, `Job` 
			WHERE `File`.`JobId` = $jobId 
			AND  `Path`.`PathId` = `File`.`PathId` 
			AND  `Filename`.`FilenameId` = `File`.`FilenameId` 
			AND  `Job`.`JobId` = `File`.`JobId` 
			ORDER BY `File`.`FileIndex` ASC;";
        $result = $this->run_query($sql_query);

		$used_types = $result->fetchAll();

        return $used_types[0]['count'];
	}
	
	public function getJobNameAndJobStatusByJobId($jobId){
		$used_types = array();
        $sql_query = "SELECT distinct `Job`.`Name`, `Job`.`JobStatus`
			FROM `Job` 
			WHERE `Job`.`JobId` = $jobId;";
        $result = $this->run_query($sql_query);

		$used_types = $result->fetchAll();
		if(count($used_types) != 0){
			$used_types = $used_types[0];

			switch ($used_types['jobstatus']) {
				case 'R':
					$used_types['jobstatus'] = 'Running';
					break;
				case 'F':
				case 'S':
				case 'M':
				case 'm':
				case 's':
				case 'j':
				case 'c':
				case 'd':
				case 't':
				case 'p':
				case 'C':
					$used_types['jobstatus'] = 'Waiting';
					break;
				case 'T':
					$used_types['jobstatus'] = 'Completed';
					break;
				case 'E':
					$used_types['jobstatus'] = 'Completed with errors';
					break;
				case 'f':
					$used_types['jobstatus'] = 'Failed';
					break;
				case 'A':
					$used_types['jobstatus'] = 'Canceled';
					break;
				default:
					$used_types['jobstatus'] = 'All';
					break;
			}
		}
		
        return $used_types;
	}
}
