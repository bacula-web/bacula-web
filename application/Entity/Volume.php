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

class Volume
{
    /**
     * @var int
     */
    private int $volbytes;

    private int $inchanger;

    private int $slot;

    /**
     * @return string
     */
    public function getVolbytes(): string
    {
        return CUtils::Get_Human_Size($this->volbytes);
    }

    public function getInchanger(): string
    {
        if ($this->inchanger === 0) {
            return '-';
        }
        return '<i class="fa fa-check" aria-hidden="true"></i>';
    }

    public function getSlot(): string|int
    {
        if ($this->inchanger === 0) {
            return 'n/a';
        }
        return $this->slot;
    }
}
