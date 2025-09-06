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

use App\Entity\Bacula\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table(name: 'Log')]
class Log
{
    #[ORM\GeneratedValue()]
    #[ORM\Column(name: "LogId", type: "integer")]
    #[ORM\Id()]
    private int $id;

    #[ORM\Column(type: "integer", name: "JobId")]
    private int $jobid;

    #[ORM\Column(type: "text", name: "LogText")]
    private string $logtext;

    #[ORM\Column(name: "Time", type: "string")]
    private string $time;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: "logs")]
    #[ORM\JoinColumn(name: "JobId", referencedColumnName: "JobId")]
    private Job $job;

    /**
     * @return string
     */
    public function getLogText(): string
    {
        return $this->logtext;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getJobid(): int
    {
        return $this->jobid;
    }

    /**
     * @return Job
     */
    public function getJob(): Job
    {
        return $this->job;
    }
}
