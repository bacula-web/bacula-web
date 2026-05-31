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

use App\Entity\Bacula\Client;
use App\Entity\Bacula\Pool;

class JobSearch
{
    /**
     * @var Pool|null
     */
    private ?Pool $pool = null;

    /**
     * @var Client|null
     */
    private ?Client $client = null;
    /**
     * @var string|null
     */
    private ?string $type = null;
    /**
     * @var string|null
     */
    private ?string $status = null;

    /**
     * @var string|null
     */
    private ?string $level = null;

    /**
     * @var DateTime|null
     */
    private ?DateTime $starttime = null;

    /**
     * @var DateTime|null
     */
    private ?DateTime $endtime = null;

    /**
     * @var string|null
     */
    private ?string $orderby = null;

    /**
     * @var string|null
     */
    private ?string $orderDirection = null;

    public function getStarttime(): ?DateTime
    {
        return $this->starttime;
    }

    public function setStarttime(?DateTime $starttime): void
    {
        $this->starttime = $starttime;
    }

    public function getEndtime(): ?DateTime
    {
        return $this->endtime;
    }

    public function setEndtime(?DateTime $endtime): void
    {
        $this->endtime = $endtime;
    }

    public function getOrderby(): ?string
    {
        return $this->orderby;
    }

    public function setOrderby(?string $orderby): void
    {
        $this->orderby = $orderby;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function setOrderDirection(?string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): void
    {
        $this->level = $level;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): void
    {
        $this->client = $client;
    }

    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    public function setPool(?Pool $pool): void
    {
        $this->pool = $pool;
    }
}

