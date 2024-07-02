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

use App\Entity\Bacula\Repository\PoolRepository;
use Symfony\Component\Validator\Constraints as Assert;

class VolumeSearch
{
    /**
     * @var PoolRepository
     */
    private static PoolRepository $poolRepository;

    /**
     * @param PoolRepository $poolRepository
     */
    public function __construct(PoolRepository $poolRepository)
    {
        self::$poolRepository = $poolRepository;
    }

    /**
     * @Assert\Choice(callback={"App\Entity\Bacula\VolumeSearch", "getPools"})
     *
     * @var Pool|null
     */
    private $pool;

    /**
     * @var string|null
     */
    private $orderBy;

    /**
     * @var string|null
     */
    private $orderDirection;

    /**
     * @var bool
     */
    private $inChanger;

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
     * @return array
     */
    public static function getPools(): array
    {
        return self::$poolRepository->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     * @return void
     */
    public function setOrderBy(string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return string|null
     */
    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    /**
     * @param string $orderDirection
     * @return void
     */
    public function setOrderDirection(string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }

    /**
     * @return bool
     */
    public function isInChanger(): bool
    {
        return $this->inChanger;
    }

    /**
     * @param bool $inChanger
     * @return void
     */
    public function setInChanger(bool $inChanger): void
    {
        $this->inChanger = $inChanger;
    }
}
