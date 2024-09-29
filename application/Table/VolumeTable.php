<?php

declare(strict_types=1);

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

namespace App\Table;

use Core\Db\Table;
use Core\Db\CDBQuery;

class VolumeTable extends Table
{
    /**
     * @var string|null
     */
    protected ?string $tablename = 'Media';

    /**
     * return disk space usage (bytes) for all volumes
     *
     * @return string
     */
    public function getDiskUsage(): string
    {
        $fields = ['SUM(Media.VolBytes) as bytes_size'];
        $statment = [
            'table' => $this->tablename,
            'fields' => $fields
        ];

        // Run SQL query
        $result = $this->run_query(CDBQuery::get_Select($statment));

        $result = $result->fetch();

        return (string) $result['bytes_size'];
    }

    /**
     * @param int $volumeId
     * @return void
     */
    public function getJobs(int $volumeId): array
    {
        $sqlquery = "SELECT DISTINCT Job.JobId as JobId,Job.Name as Name,Job.StartTime as StartTime,
            Job.Type as Type,Job.Level as Level,Job.JobFiles as Files,
            Job.JobBytes as Bytes,Job.JobStatus as Status
            FROM Media,JobMedia,Job
            WHERE Media.MediaId = $volumeId
            AND Media.MediaId=JobMedia.MediaId
            AND JobMedia.JobId=Job.JobId
            ORDER by Job.StartTime";

        return $this->select($sqlquery);
    }
}
