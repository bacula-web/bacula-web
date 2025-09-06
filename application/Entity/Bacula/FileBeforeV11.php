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
use App\Entity\Bacula\Repository\FileBeforeV11Repository;

#[ORM\Entity(repositoryClass: FileBeforev11Repository::class)]
#[ORM\Table(name: 'File')]
class FileBeforeV11
{
    #[ORM\Column(name: "FileId", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(name: "JobId", type: "integer")]
    private int $jobid;

    #[ORM\Column(name: "PathId", type: "integer")]
    private ?int $pathid;

    #[ORM\Column(name: "FileIndex", type: "integer")]
    private ?int $fileindex;

    #[ORM\JoinColumn(name: "JobId", referencedColumnName: "JobId")]
    #[ORM\ManyToOne(targetEntity: "App\Entity\Bacula\Job", inversedBy: "files")]
    private Job $job;

    #[ORM\JoinColumn(name: "PathId", referencedColumnName: "PathId")]
    #[ORM\OneToOne(targetEntity: Path::class)]
    private Path $path;

    #[ORM\JoinColumn(name: "FilenameId", referencedColumnName: "FilenameId")]
    #[ORM\OneToOne(targetEntity: Filename::class)]
    private Filename $filename;

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
    public function getFileindex(): int|null
    {
        return $this->fileindex;
    }

    /**
     * @return int|null
     */
    public function getPathid(): int|null
    {
        return $this->pathid;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename->getName();
    }

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return $this->path;
    }
}
