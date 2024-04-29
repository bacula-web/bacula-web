<?php

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of the Bacula-Web project.
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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Bacula\Repository\JobRepository;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 * @ORM\Table(name="Job")
 */
class Job
{
    private const J_COMPLETED = 'T';
    private const J_NOT_RUNNING = 'C';
    private const J_RUNNING = 'R';
    private const J_BLOCKED = 'B';
    private const J_COMPLETED_ERROR = 'E';
    private const J_NOT_FATAL_ERROR = 'e';
    private const J_FATAL = 'f';
    private const J_CANCELED = 'A';
    private const J_WAITING_CLIENT = 'F';
    private const J_WAITING_SD = 'S';
    private const J_WAITING_NEW_MEDIA = 'S';
    private const J_WAITING_MOUNT_MEDIA = 'M';
    private const J_WAITING_STORAGE_RES = 's';
    private const J_WAITING_JOB_RES = 'j';
    private const J_WAITING_CLIENT_RES = 'c';
    private const J_WAITING_MAX_JOBS = 'd';
    private const J_WAITING_START_TIME = 't';
    private const J_WAITING_HIGH_PR_JOB = 'p';
    private const J_VERIFY_FOUND_DIFFERENCES = 'D';

    private const JOB_TYPES = [
        'B' => 'Backup',
        'M' => 'Migrated',
        'V' => 'Verify',
        'R' => 'Restore',
        'D' => 'Admin',
        'A' => 'Archive',
        'C' => 'Copy',
        'g' => 'Migration'
    ];

    private const JOB_STATUS_ICONS = [
        'Running' => 'fa-solid fa-play',
        'Completed' => 'fa-solid fa-check',
        'Canceled' => 'fa-solid fa-power-off',
        'WithErrors' => 'fa-solid fa-triangle-exclamation',
        'WithFatalErrors' => 'fa-solid fa-xmark',
        'Waiting' => 'fa-solid fa-clock',
        'Unknown' => 'fa-solid fa-question'
    ];

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

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="JobId")
     * @ORM\GeneratedValue()
     *
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="string", name="Name")
     *
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=1, name="Level")
     *
     * @var string
     */
    private string $level;

    /**
     * @ORM\Column(type="integer", name="JobBytes")
     *
     * @var int
     */
    private int $jobbytes;

    /**
     * @ORM\Column(type="integer", name="ReadBytes")
     *
     * @var int
     */
    private int $readbytes;

    /**
     * @ORM\Column(type="integer", name="JobFiles")
     *
     * @var int
     */
    private int $jobfiles;

    /**
     * @ORM\Column(type="string", length=1, name="Type")
     *
     * @var string
     */
    private string $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bacula\Pool")
     * @ORM\JoinColumn(name="PoolId", referencedColumnName="PoolId")
     *
     * @var Pool|null
     */
    private ?Pool $pool;

    /**
     * @ORM\Column(type="integer", name="PoolId")
     *
     * @var int|null
     */
    private ?int $poolid;

    /**
     * @ORM\OneToOne(targetEntity="Status")
     * @ORM\JoinColumn(name="JobStatus", referencedColumnName="JobStatus")
     *
     * @var Status
     */
    private Status $status;

    /**
     * @ORM\Column(type="datetime", name="StartTime")
     *
     * @var DateTime|null
     */
    private ?DateTime $starttime;

    /**
     * @ORM\Column(type="datetime", name="EndTime")
     *
     * @var DateTime|null
     */
    private ?DateTime $endtime;

    /**
     * @ORM\Column(type="datetime", name="SchedTime")
     *
     * @var DateTime
     */
    private DateTime $scheduledTime;

    /**
     * @var string
     */
    private string $elapsedTime;

    /**
     * @var string
     */
    private string $statusicon;

    /**
     * @var int
     */
    private int $speed;

    /**
     * @var float
     */
    private float $compression;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bacula\Log", mappedBy="job")
     *
     * @var Collection
     */
    private Collection $logs;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="ClientId", referencedColumnName="ClientId")
     *
     * @var Client
     */
    private Client $client;

    /**
     * @ORM\Column(type="integer", name="ClientId")
     * @var int
     */
    private int $clientid;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bacula\File", mappedBy="job")
     * @ORM\JoinColumn(name="JobId", referencedColumnName="JobId")
     *
     * @var Collection
     */
    private Collection $files;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return self::JOB_LEVELS[$this->level];
    }

    /**
     * @return int
     */
    public function getJobBytes(): int
    {
        return $this->jobbytes;
    }

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
    public function getType(): string
    {
        return self::JOB_TYPES[$this->type];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
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
    public function getElapsedTime(): string
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
    public function getJobfiles(): int
    {
        return $this->jobfiles;
    }

    /**
     * @return int
     */
    public function getSpeed(): int
    {
        $elapsedSeconds = 0;

        if ($this->endtime) {
            $elapsedSeconds = $this->endtime->diff($this->starttime)->s;
        } else {
            $this->speed = 0;
        }

        if ($elapsedSeconds > 0) {
            $this->speed = (int) floor($this->jobbytes / $elapsedSeconds);
        } else {
            $this->speed = 0;
        }
        return $this->speed;
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
     * @return Pool|null
     */
    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    /**
     * @return Collection
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    /**
     * @return int
     */
    public function getReadbytes(): int
    {
        return $this->readbytes;
    }

    /**
     * @return string
     */
    public function getStatusicon(): string
    {
        switch ($this->status->getStatus()) {
            case self::J_RUNNING:
                $this->statusicon = self::JOB_STATUS_ICONS['Running'];
                break;
            case self::J_COMPLETED:
                $this->statusicon = self::JOB_STATUS_ICONS['Completed'];
                break;
            case self::J_CANCELED:
                $this->statusicon = self::JOB_STATUS_ICONS['Canceled'];
                break;
            case self::J_VERIFY_FOUND_DIFFERENCES:
            case self::J_COMPLETED_ERROR:
                $this->statusicon = self::JOB_STATUS_ICONS['WithErrors'];
                break;
            case self::J_FATAL:
                $this->statusicon = self::JOB_STATUS_ICONS['WithFatalErrors'];
                break;
            case self::J_WAITING_CLIENT:
            case self::J_WAITING_SD:
            case self::J_WAITING_MOUNT_MEDIA:
            case self::J_WAITING_NEW_MEDIA:
            case self::J_WAITING_STORAGE_RES:
            case self::J_WAITING_JOB_RES:
            case self::J_WAITING_CLIENT_RES:
            case self::J_WAITING_MAX_JOBS:
            case self::J_WAITING_START_TIME:
            case self::J_NOT_RUNNING:
                $this->statusicon = self::JOB_STATUS_ICONS['Waiting'];
                break;
            default:
                $this->statusicon = self::JOB_STATUS_ICONS['Unknown'];
        }
        return $this->statusicon;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return int
     */
    public function getClientid(): int
    {
        return $this->clientid;
    }

    /**
     * @return int|null
     */
    public function getPoolid(): int
    {
        return $this->poolid;
    }
}
