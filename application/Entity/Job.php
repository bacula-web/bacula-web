<?php

declare(strict_types=1);

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

namespace App\Entity;

use Core\Utils\CUtils;

class Job
{
    /**
     * @var string
     */
    private string $level;

    /**
     * @var int
     */
    private int $jobbytes;

    /**
     * @return string
     */
    public function getLevel(): string
    {
        $joblevels = [
            'D' => 'Differential',
            'I' => 'Incremental',
            'F' => 'Full',
            'V' => 'InitCatalog',
            'C' => 'Catalog',
            'O' => 'VolumeToCatalog',
            'd' => 'DiskToCatalog',
            'A' => 'Data'
        ];

        return $joblevels[$this->level];
    }

    /**
     * @return string
     */
    public function getJobBytes(): string
    {
        return CUtils::Get_Human_Size($this->jobbytes);
    }
}
