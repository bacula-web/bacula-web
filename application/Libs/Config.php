<?php

/**
 * Copyright (C) 2024-present Davide Franco
 *
 * This file is part of Bacula-Web project.
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

namespace App\Libs;

class Config
{
    /**
     * @var array
     */
    private array $configData;

    /**
     * @param array $configData
     */
    public function __construct(array $configData)
    {
        $this->configData = $configData;
    }

    public function get(string $key, $default = null)
    {
        if (isset($this->configData[$key])) {
            return $this->configData[$key];
        } elseif (null !== $default) {
            return $default;
        }
        return null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->configData[$key]);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->configData;
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->configData[$key] = $value;
    }

    /**
     * @param string $filename
     * @return false|int
     */
    public function write(string $filename)
    {
        $data = var_export($this->configData, true);
        return file_put_contents($filename, '<?php' . PHP_EOL . PHP_EOL . '$config = ' . $data . ';' . PHP_EOL);
    }

    /**
     *
     * @return int
     */
    public function countArrays(): int
    {
        $arraysCount = 0;
        foreach ($this->all() as $value ) {
            if (is_array($value)) {
                $arraysCount += 1;
            }
        }
        return $arraysCount;
    }

    /**
     * Return label key if key is an array (used for Bacula database catalog)
     *
     * @return array
     */
    public function getArrays(): array
    {
        $arrays = [];

        foreach ($this->configData as $parameter) {
            if (is_array($parameter)) {
                $arrays[] = $parameter;
            }
        }

        return $arrays;
    }
}
