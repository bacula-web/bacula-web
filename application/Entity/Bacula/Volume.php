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

use App\Entity\Bacula\Repository\VolumeRepository;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VolumeRepository::class)]
#[ORM\Table(name: 'Media')]
class Volume
{
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

    #[ORM\Id]
    #[ORM\Column(name: 'MediaId', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'VolumeName', type: 'string')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Pool::class, inversedBy: 'volumes')]
    #[ORM\JoinColumn(name: 'PoolId', referencedColumnName: 'PoolId')]
    private Pool $pool;

    #[ORM\Column(name: 'VolBytes', type: 'integer')]
    private int $volbytes;

    #[ORM\Column(name: 'VolFiles', type: 'integer')]
    private int $volfiles;

    #[ORM\Column(name: 'VolJobs', type: 'integer')]
    private int $voljobs;

    #[ORM\Column(name: "PoolId", type: "integer")]
    private int $poolId;

    #[ORM\Column(name: "FirstWritten", type: "string")]
    private string $firstwritten;

    #[ORM\Column(name: "LastWritten", type: "string")]
    private string $lastwritten;

    #[ORM\Column(name: 'VolMounts', type: 'integer')]
    private int $volmounts;

    #[ORM\Column(type: 'integer')]
    private int $slot;

    #[ORM\Column(type: 'boolean')]
    private bool $inchanger;

    #[ORM\Column(name: 'MediaType', type: 'string')]
    private string $mediatype;

    #[ORM\Column(name: 'VolStatus', type: 'string')]
    private string $status;

    /**
     * @var string
     */
    private string $statusIcon;

    #[ORM\Column(name: 'VolRetention', type: 'integer')]
    private int $volretention;

    /**
     * @var string
     */
    private string $expirationDate;

    /**
     * @var int volume expiration in xxx
     */
    private int $expire = 0;

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
     * @return int
     */
    public function getVolfiles(): int
    {
        return $this->volfiles;
    }

    /**
     * @return int
     */
    public function getPoolId(): int
    {
        return $this->poolId;
    }

    /**
     * @return Pool
     */
    public function getPool(): Pool
    {
        return $this->pool;
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
    public function getVoljobs(): int
    {
        return $this->voljobs;
    }

    /**
     * @return string
     */
    public function getFirstWritten(): string
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
     * @return int
     */
    public function getSlot(): int
    {
        return $this->slot;
    }

    /**
     * @return bool
     */
    public function isInchanger(): bool
    {
        return $this->inchanger;
    }

    /**
     * @return string
     */
    public function getInChangerStatus(): string
    {
        if (!$this->inchanger) {
            return '-';
        }
        return '<i class="fa fa-check" aria-hidden="true"></i>';
    }

    /**
     * @return string
     */
    public function getMediatype(): string
    {
        return $this->mediatype;
    }

    /**
     * @return string
     */
    public function getLastwritten(): string
    {
        return $this->lastwritten;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getVolretention(): int
    {
        return $this->volretention;
    }

    public function getExpire(): int
    {
        if ($this->status === 'Full' || $this->status === 'Used') {
            $this->expire = strtotime($this->lastwritten) + $this->volretention;
        }

        return $this->expire;
    }

    /**
     * @return string
     */
    public function getExpirationDate(): string
    {
        $expireOn = Carbon::createFromTimestamp($this->expire);

        $this->expirationDate = (string) $expireOn->diffInDays();

        return $this->expirationDate;
    }

    public function getStatusIcon(): string
    {
        return self::VOLUME_STATUS_ICON[$this->status];
    }
}
