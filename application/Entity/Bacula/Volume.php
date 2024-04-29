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

use App\Entity\Bacula\Repository\VolumeRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VolumeRepository::class)
 * @ORM\Table(name="Media")
 */
class Volume
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="MediaId")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", name="PoolId")
     */
    private int $poolId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bacula\Pool", inversedBy="volumes")
     * @ORM\JoinColumn(name="PoolId", referencedColumnName="PoolId")
     *
     * @var Pool
     */
    private Pool $pool;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private bool $inchanger;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int $slot
     */
    private int $slot;

    /**
     * @ORM\Column(type="integer")
     */
    private int $mediaId;

    /**
     * @ORM\Column(type="string", name="VolumeName")
     */
    private string $name;

    /**
     * @ORM\Column(type="integer", name="VolBytes")
     */
    private $volbytes;

    /**
     * @ORM\Column(type="integer", name="VolFiles")
     */
    private int $volfiles;

    /**
     * @ORM\Column(type="string", name="MediaType")
     */
    private string $mediatype;

    /**
     * @var int
     */
    private int $expire = 0;

    /**
     * One can have one to several volume(s)
     * One volume can be used by Many jobs
     */

    private const VOLUME_STATUS_ICON = [
        'Full' => 'fa-battery-full',
        'Archive' => 'fa-file-archive-o',
        'Append' => 'fa-battery-quarter',
        'Recycle' => 'fa-recycle',
        'Read-Only' => 'fa-lock',
        'Disabled' => 'fa-ban',
        'Error' => 'fa-times-circle',
        'Busy' => 'fa-clock-o',
        'Used' => 'fa-battery-quarter',
        'Purged' => 'fa-battery-empty'
    ];

    /**
     * @ORM\Column(type="string", name="VolStatus")
     */
    private string $status;

    /**
     * @var string
     */
    private string $statusicon;

    /**
     * @ORM\Column(type="integer", name="VolJobs")
     */
    private int $voljobs;

    /**
     * @ORM\Column(type="string", name="LastWritten")
     *
     * @var string
     */
    private string $lastwritten;

    /**
     * @ORM\Column(type="datetime", name="FirstWritten")
     *
     * @var DateTime
     */
    private DateTime $firstwritten;

    /**
     * @ORM\Column(type="integer", name="VolMounts")
     */
    private $volmounts;

    /**
     * @ORM\Column(type="string", name="VolRetention")
     *
     * @var int
     */
    private $retention;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
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
    public function getVolfiles(): int
    {
        return $this->volfiles;
    }

    /**
     * @return int
     */
    public function getVoljobs(): int
    {
        return $this->voljobs;
    }

    /**
     * @return string
     */
    public function getMediaType(): string
    {
        return $this->mediatype;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getVolbytes(): int
    {
        return $this->volbytes;
    }

    /**
     * @ORM\Column(type="string")
     *
     * @return bool
     */
    public function getInchanger(): bool
    {
        return $this->inchanger;
    }

    /**
     * @ORM\Column(type="integer")
     *
     * @return int
     */
    public function getSlot(): int
    {
        return $this->slot;
    }

    /**
     * @return int
     */
    public function getExpire(): int
    {
        if ($this->status === 'Full' || $this->status === 'Used') {
            return (
                strtotime($this->lastwritten) + $this->retention);
        } else {
            return 0;
        }
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function getLastwritten(): string
    {
        return $this->lastwritten;
    }

    public function getRetention(): int
    {
        return $this->retention;
    }

    /**
     * @return Pool
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }

    public function getStatusicon(): string
    {
        return self::VOLUME_STATUS_ICON[$this->status];
    }

    /**
     * @return DateTime
     */
    public function getFirstWritten(): DateTime
    {
        return $this->firstwritten;
    }

    /**
     * @return int
     */
    public function getVolmounts(): int
    {
        return $this->volmounts;
    }

    /**
     * @return int|null
     */
    public function getPoolId(): ?int
    {
        return $this->poolId;
    }

    public function isInchanger(): ?bool
    {
        return $this->inchanger;
    }

    /**
     * @return int|null
     */
    public function getMediaId(): ?int
    {
        return $this->mediaId;
    }
}
