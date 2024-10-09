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

use App\Entity\Bacula\Repository\JobRepository;

class JobSearch
{
    /**
     * @var JobRepository
     */
    private JobRepository $jobRepository;

    /**
     * @var string|null
     */
    private $orderBy;

    /**
     * @var string|null
     */
    private $orderByDirection;

    /**
     * @var Client|null
     */
    private $client;

    /**
     * @var string|null
     */
    private $level;

    /**
     * @var string|null
     */
    private $status;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var Pool|null
     */
    private $pool;

    /**
     * @var string|null
     */
    private $starttime;

    /**
     * @var string|null
     */
    private $endtime;

    /**
     * @param JobRepository $jobRepository
     */
    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    /**
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    /**
     * @param string|null $orderBy
     * @return void
     */
    public function setOrderBy(?string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return Client|null
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param Client|null $client
     * @return void
     */
    public function setClient(?Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return string|null
     */
    public function getLevel(): ?string
    {
        return $this->level;
    }

    /**
     * @param string|null $level
     * @return void
     */
    public function setLevel(?string $level): void
    {
        $this->level = $level;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return void
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Pool|null
     */
    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    /**
     * @param Pool|null $pool
     * @return void
     */
    public function setPool(?Pool $pool): void
    {
        $this->pool = $pool;
    }

    /**
     * @return string|null
     */
    public function getStarttime(): ?string
    {
        return $this->starttime;
    }

    /**
     * @param string|null $starttime
     * @return void
     */
    public function setStarttime(?string $starttime): void
    {
        $this->starttime = $starttime;
    }

    /**
     * @return string|null
     */
    public function getEndtime(): ?string
    {
        return $this->endtime;
    }

    /**
     * @param string|null $endtime
     * @return void
     */
    public function setEndtime(?string $endtime): void
    {
        $this->endtime = $endtime;
    }

    /**
     * @return string|null
     */
    public function getOrderByDirection(): ?string
    {
        return $this->orderByDirection;
    }

    /**
     * @param string|null $orderByDirection
     * @return void
     */
    public function setOrderByDirection(?string $orderByDirection): void
    {
        $this->orderByDirection = $orderByDirection;
    }
}
