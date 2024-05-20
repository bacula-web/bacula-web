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

namespace App\Service\Helper;

/**
 * Number helper class
 */
class Number
{
    /**
     * @param $size
     * @param int $decimal
     * @param string $unit
     * @param bool $display_unit
     * @return string
     */
    public static function humanReadable(
        $size,
        int $decimal = 2,
        string $unit = 'auto',
        bool $display_unit = true
    ): string {
        $unit_id = 0;
        $lisible = false;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $hsize = $size;

        if ($unit == 'auto') {
            while (!$lisible) {
                if ($hsize >= 1024) {
                    $hsize /= 1024;
                    $unit_id++;
                } else {
                    $lisible = true;
                }
            }
        } else {
            $exp = array_keys($units, $unit);

            $unit_id = current($exp);
            $hsize /= pow(1024, $unit_id);
        }

        $hsize = number_format((float)$hsize, $decimal, '.', '');

        // Append unit or not
        if ($display_unit) {
            $hsize .= ' ' . $units[$unit_id];
        }

        return $hsize;
    }
}
