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

namespace App\Entity\Bacula;

use App\Entity\Bacula\Repository\JobRepository;
use Carbon\Carbon;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\Table(name: 'Job')]
class Job
{
    private const JOB_STATUS_NOT_RUNNING = 'C';
    private const JOB_STATUS_RUNNING = 'R';
    private const JOB_STATUS_BLOCKED = 'B';
    private const JOB_STATUS_COMPLETED = 'T';
    private const JOB_STATUS_COMPLETED_ERROR = 'E';
    private const JOB_STATUS_NO_FATAL_ERROR = 'e';
    private const JOB_STATUS_FATAL = 'f';
    private const JOB_STATUS_CANCELED = 'A';
    private const JOB_STATUS_WAITING_CLIENT = 'F';
    private const JOB_STATUS_WAITING_SD = 'S';
    private const JOB_STATUS_WAITING_NEW_MEDIA = 'm';
    private const JOB_STATUS_WAITING_MOUNT_MEDIA = 'M';
    private const JOB_STATUS_WAITING_STORAGE_RES = 's';
    private const JOB_STATUS_WAITING_JOB_RES = 'j';
    private const JOB_STATUS_WAITING_CLIENT_RES = 'c';
    private const JOB_STATUS_WAITING_MAX_JOBS = 'd';
    private const JOB_STATUS_WAITING_START_TIME = 't';
    private const JOB_STATUS_WAITING_HIGH_PR_JOB = 'p';
    private const JOB_STATUS_VERIFY_FOUND_DIFFERENCES = 'D';

    private const JOB_LEVELS = [
        'D' => 'Differential',
        'I' => 'Incremental',
        'F' => 'Full',
        'V' => 'InitCatalog',
        'C' => 'Catalog',
        'O' => 'VolumeToCatalog',
        'd' => 'DiskToCatalog',
        'A' => 'Data'
        ];

    #[ORM\Id]
    #[ORM\Column(name: 'JobId', type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(name: 'Name', type: 'string')]
    private string $name;

    #[ORM\Column(name: "ClientId", type: "integer")]
    private int $clientid;

    #[ORM\JoinColumn(name: "ClientId", referencedColumnName: "ClientId")]
    #[ORM\ManyToOne(targetEntity: Client::class)]
    private Client $client;

    #[ORM\Column(name: 'Level', type: 'string')]
    private string $level;

    #[ORM\Column(name: 'JobFiles', type: 'integer')]
    private int $jobfiles;

    #[ORM\Column(name: 'JobBytes', type: 'integer')]
    private int $jobbytes;

    #[ORM\Column(name: 'ReadBytes', type: 'integer')]
    private int $readbytes;

    #[ORM\OneToOne(targetEntity: Status::class)]
    #[ORM\JoinColumn(name: "JobStatus", referencedColumnName: "JobStatus")]
    private Status $status;

    /**
     * @var string
     */
    private string $statusIcon;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: Log::class)]
    private Collection $logs;

    #[ORM\ManyToOne(targetEntity: Pool::class)]
    #[ORM\JoinColumn(name: "PoolId", referencedColumnName: "PoolId")]
    private ?Pool $pool;

    #[ORM\Column(name: 'PoolId', type: 'integer')]
    private int $poolid;

    #[ORM\Column(name: 'SchedTime', type: 'datetime')]
    private DateTime $scheduledTime;

    #[ORM\Column(name: 'StartTime', type: 'datetime')]
    private ?DateTime $starttime;

    #[ORM\Column(name: 'EndTime', type: 'datetime')]
    private ?DateTime $endtime;

    /**
     * @var string
     */
    private string $elapsedTime;

    /**
     * @var int
     */
    private int $bitrate;

    /**
     * @var float
     */
    private float $compression;

    #[ORM\Column(name: 'Type', type: 'string', length: 1)]
    private string $type;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this::JOB_LEVELS[$this->level];
    }

    /**
     * @return int
     */
    public function getJobbytes(): int
    {
        return $this->jobbytes;
    }

    /**
     * @return int
     */
    public function getReadbytes(): int
    {
        return $this->readbytes;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return Collection
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    /**
     * @return Pool|null
     */
    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    /**
     * @return DateTime
     */
    public function getScheduledTime(): DateTime
    {
        return $this->scheduledTime;
    }

    /**
     * @return DateTime|null
     */
    public function getStarttime(): ?DateTime
    {
        return $this->starttime;
    }

    /**
     * @return DateTime|null
     */
    public function getEndtime(): ?DateTime
    {
        return $this->endtime;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getJobfiles(): int
    {
        return $this->jobfiles;
    }

    /**
     * @return int
     */
    public function getClientid(): int
    {
        return $this->clientid;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getStatusIcon(): string
    {
        switch ($this->status->getStatus()) {
            case $this::JOB_STATUS_RUNNING:
                $statusIcon = 'fa-solid fa-play';
                break;
            case $this::JOB_STATUS_COMPLETED:
                $statusIcon = 'fa-solid fa-check';
                break;
            case $this::JOB_STATUS_CANCELED:
                $statusIcon = 'fa-solid fa-power-off';
                break;
            case $this::JOB_STATUS_VERIFY_FOUND_DIFFERENCES:
            case $this::JOB_STATUS_COMPLETED_ERROR:
                $statusIcon = 'fa-solid fa-triangle-exclamation';
                break;
            case $this::JOB_STATUS_FATAL:
                $statusIcon = 'fa-solid fa-xmark';
                break;
            case $this::JOB_STATUS_WAITING_CLIENT:
            case $this::JOB_STATUS_WAITING_SD:
            case $this::JOB_STATUS_WAITING_MOUNT_MEDIA:
            case $this::JOB_STATUS_WAITING_NEW_MEDIA:
            case $this::JOB_STATUS_WAITING_STORAGE_RES:
            case $this::JOB_STATUS_WAITING_JOB_RES:
            case $this::JOB_STATUS_WAITING_CLIENT_RES:
            case $this::JOB_STATUS_WAITING_MAX_JOBS:
            case $this::JOB_STATUS_WAITING_START_TIME:
            case $this::JOB_STATUS_NOT_RUNNING:
                $statusIcon = 'fa-solid fa-clock';
                break;
            default:
                // TODO: change this icon to generic one (using question mark for now)
                $statusIcon = 'fa-solid fa-question';
        }
        return $statusIcon;
    }

    /**
     * @return string
     */
    public function getElapsedtime(): string
    {
        if ($this->starttime && $this->endtime) {
            $diff = $this->starttime->diff($this->endtime);

            if ($diff->d > 0) {
                $this->elapsedTime = $diff->format('%d day(s), %H:%I:%S');
            } else {
                $this->elapsedTime = $diff->format('%H:%I:%S');
            }
        } else {
            $this->elapsedTime = '';
        }

        return $this->elapsedTime;
    }

    /**
     * @return int
     */
    public function getBitrate(): int
    {
        if ($this->endtime) {
            $start = new Carbon($this->getStarttime());
            $end = new Carbon($this->getEndtime());

            $elapsedSeconds = $start->diffInSeconds($end);
        } else {
            $elapsedSeconds = 0;
        }

        if ($elapsedSeconds > 0) {
            return (int) floor($this->jobbytes / $elapsedSeconds);
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getCompression(): float
    {
        if ($this->jobbytes > 0 && $this->readbytes > 0) {
            $this->compression = (1 - ($this->jobbytes / $this->readbytes));
        } else {
            $this->compression = 0;
        }

        return $this->compression;
    }

    /**
     * @return int
     */
    public function getPoolid(): int
    {
        return $this->poolid;
    }
}
