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

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provide a Twig function which convert a size (bytes, files, etc.) and divide it automatically
 * using the best unit such as KB, MG, TB, etc.
 */
class Number extends AbstractExtension
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Number';
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('readable_size', [$this, 'readableSize'])
        ];
    }

    /**
     * @param int $number
     * @param int $decimal
     * @param string $unit
     * @param bool $display_unit
     * @return string
     */
    public function readableSize(int $number, int $decimal = 2, string $unit = 'auto', bool $display_unit = true): string
    {
        $unit_id = 0;
        $lisible = false;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $humanSize = $number;

        switch ($unit) {
            case 'auto':
                while (!$lisible) {
                    if ($humanSize >= 1024) {
                        $humanSize /= 1024;
                        $unit_id++;
                    } else {
                        $lisible = true;
                    }
                } // end while
                break;

            default:
                $exp = array_keys($units, $unit);
                $unit_id = current($exp);
                $humanSize /= pow(1024, $unit_id);
                break;
        }
        // Format human-readable value (with dot for decimal separator)
        $humanSize = number_format((float)$humanSize, $decimal, '.', '');

        // Append unit or not
        if ($display_unit) {
            $humanSize .= ' ' . $units[$unit_id];
        }

        return $humanSize;
    }
}