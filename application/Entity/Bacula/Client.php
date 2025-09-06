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

use App\Entity\Bacula\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'Client')]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'ClientId', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'Name', type: 'string')]
    private string $name;

    #[ORM\Column(name: 'Uname', type: 'string')]
    private string $uname;

    #[ORM\Column(name: 'FileRetention', type: 'integer')]
    private int $fileRetention;

    #[ORM\Column(name: 'JobRetention', type: 'integer')]
    private int $jobRetention;

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
     * @return string
     */
    public function getUname(): string
    {
        return $this->uname;
    }

    /**
     * @return int
     */
    public function getFileRetention(): int
    {
        return $this->fileRetention;
    }

    /**
     * @return int
     */
    public function getJobRetention(): int
    {
        return $this->jobRetention;
    }
}
