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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PoolRepository::class)
 * @ORM\Table(name="Pool")
 */
class Pool
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="PoolId")
     * @ORM\GeneratedValue
     */
    private int $id;

    /**
     * @ORM\Column(type="string", name="Name")
     *
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="integer")
     */
    private int $numvols;

    /**
     * @ORM\OneToMany(targetEntity="Volume", mappedBy="pool")
     *
     * @var Collection
     */
    private Collection $volumes;

    public function __construct()
    {
        $this->volumes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name . ' - ' . $this->numvols . ' volume(s)';
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getNumVols(): int
    {
        return $this->numvols;
    }
}
