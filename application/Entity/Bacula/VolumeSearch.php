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

class VolumeSearch
{
    /**
     * @var Pool|null
     */
    private $pool;

    /**
     * @var string|null
     */
    private $orderby;

    /**
     * @var string|null
     */
    private $orderDirection;

    /**
     * @var bool|null
     */
    private $inChanger;

    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    public function setPool(Pool $pool): void
    {
        $this->pool = $pool;
    }

    public function getOrderby(): ?string
    {
        return $this->orderby;
    }

    public function setOrderby(string $orderby): void
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

    public function getInChanger(): ?bool
    {
        return $this->inChanger;
    }

    public function setInChanger(?bool $inChanger): void
    {
        $this->inChanger = $inChanger;
    }
}