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

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Bacula\Repository\FileRepository;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)(repositoryClass=FileRepository::class)
 * @ORM\Table(name="File")
 */
class File
{
    /**
     * @ORM\GeneratedValue()
     * @ORM\Id()
     * @ORM\Column(type="integer", name="FileId")
     *
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", name="JobId")
     *
     * @var int
     */
    private int $jobid;

    /**
     * @ORM\Column(type="integer", name="PathId")
     *
     * @var int|null
     */
    private ?int $pathid;

    /**
     * @ORM\Column(type="integer", name="FileIndex")
     *
     * @var int|null
     */
    private ?int $fileindex;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bacula\Job", inversedBy="files")
     * @ORM\JoinColumn(name="JobId", referencedColumnName="JobId")
     */
    private Job $job;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bacula\Path")
     * @ORM\JoinColumn(name="PathId", referencedColumnName="PathId")
     *
     * @var Path
     */
    private Path $path;

    /**
     * @ORM\Column(type="string", name="Filename")
     *
     * @var string
     */
    private string $filename;

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
     * @return int|null
     */
    public function getFileindex(): int
    {
        return $this->fileindex;
    }

    /**
     * @return int|null
     */
    public function getPathid(): int
    {
        return $this->pathid;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return $this->path;
    }

    /**
     * @return Job
     */
    public function getJob(): Job
    {
        return $this->job;
    }
}
