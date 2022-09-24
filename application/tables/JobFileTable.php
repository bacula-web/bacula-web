<?php

/**
 * Copyright (C) 2021,2022 Davide Franco
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

/**
 * Description of JobFileTable class
 *
 * @author Gabriele Orlando
 * @author Davide Franco
 * @copyright 2018-2021 Gabriele Orlando
 */

namespace App\Tables;

use Core\Db\Database;
use Core\Db\CDBQuery;
use Core\Db\DatabaseFactory;
use Core\Db\Table;

class JobFileTable extends Table
{
    protected $tablename = 'File';

    public function getJobFiles($jobId, $limit, $offset, $filename = '')
    {
        $catalog = new CatalogTable(DatabaseFactory::getDatabase());

        // Catalog version prior to Bacula 11.0.x
        if ($catalog->getCatalogVersion() < 1016) {
            $fields = array('Job.Name', 'Job.JobStatus', 'File.FileIndex', 'Path.Path', 'Filename.Name AS Filename');
            $where = array("File.JobId = $jobId");
            if (! empty($filename)) {
                $where[] = "(Filename.Name LIKE '%$filename%' OR Path.Path LIKE '%$filename%' OR concat(Path.Path, '', Filename.Name) = '$filename')";
            }

            $orderby = 'File.FileIndex ASC';
            $sqlQuery = CDBQuery::get_Select(array('table' => $this->tablename,
                'fields' => $fields,
                'join' =>   array(  array('table' => 'Path', 'condition' => 'Path.PathId = File.PathId'),
                                    array('table' => 'Filename', 'condition' => 'File.FilenameId = Filename.FilenameId'),
                                      array('table' => 'Job', 'condition' => 'Job.JobId = File.JobId') ),
                  'where' => $where,
                  'orderby' => $orderby,
                  'limit' => array( 'count' => $limit, 'offset' => $offset*$limit),
                  'offset' => ($offset*$limit)
              ), $this->cdb->getDriverName());
        } else {
            $fields = array('Job.Name', 'Job.JobStatus', 'File.FileIndex', 'Path.Path', 'File.Filename AS Filename');
            $where = array("File.JobId = $jobId");

            if (!empty($filename)) {
                $where[] = "(File.Filename LIKE '%$filename%' OR Path.Path LIKE '%$filename%' OR concat(Path.Path, '', File.Filename) = '$filename')";
            }

            $orderby = 'File.FileIndex ASC';
            $sqlQuery = CDBQuery::get_Select(array('table' => $this->tablename,
                'fields' => $fields,
                'join' =>   array(  array('table' => 'Path', 'condition' => 'Path.PathId = File.PathId'),
                                    array('table' => 'Job', 'condition' => 'Job.JobId = File.JobId') ),
                'where' => $where,
                'orderby' => $orderby,
                'limit' => array( 'count' => $limit, 'offset' => $offset*$limit),
                'offset' => ($offset*$limit)
            ), $this->cdb->getDriverName());
        }

        return $this->run_query($sqlQuery)->fetchAll();
    }

    public function countJobFiles($jobId, $filename = '')
    {
        $database = new Database();
        $catalog = new CatalogTable($database);

        if ($catalog->getCatalogVersion() < 1016) {
            $sql_query = "SELECT COUNT(*) as count
    			FROM File, Path, Filename, Job
    			WHERE File.JobId = $jobId
    			AND  Path.PathId = File.PathId
    			AND  Filename.FilenameId = File.FilenameId
                AND  Job.JobId = File.JobId ";

            // Filter by filename if requested
            if (!empty($filename)) {
                $sql_query .= "AND (Filename.Name LIKE '%$filename%' OR Path.Path LIKE '%$filename%' OR concat(Path.Path, '', Filename.Name) = '$filename') ";
            }
        } else {
            $sql_query = "SELECT COUNT(*) as count
                FROM File, Path, Job
                WHERE File.JobId = $jobId
                AND  Path.PathId = File.PathId
                      AND  Job.JobId = File.JobId ";

            // Filter by filename if requested
            if (!empty($filename)) {
                $sql_query .= "AND (File.Filename LIKE '%$filename%' OR Path.Path LIKE '%$filename%' OR concat(Path.Path, '', File.Filename) = '$filename') ";
            }
        }

        $sql_query .= ";";

        $result = $this->run_query($sql_query);

        $used_types = $result->fetchAll();

        return $used_types[0]['count'];
    }

    public function getJobNameAndJobStatusByJobId($jobId)
    {
        $sql_query = "SELECT distinct Job.Name, Job.JobStatus FROM Job WHERE Job.JobId = $jobId;";

        $result = $this->run_query($sql_query);

        $used_types = $result->fetchAll();

        if(!empty($used_types)) 
        {
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
