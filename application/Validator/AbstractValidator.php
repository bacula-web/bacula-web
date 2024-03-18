<?php

/**
 * Copyright (C) 2024-present Davide Franco
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

namespace App\Validator;

use Valitron\Validator;

abstract class AbstractValidator
{
    protected Validator $validator;
    protected array $rules;

    /**
     * @return void
     */
    abstract protected function setRules(): void;

    /**
     * @param array $parameters
     * @param array $fields
     */
    public function __construct(array $parameters, array $fields)
    {
        $this->validator = new Validator($parameters, $fields);
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return $this->validator->validate();
    }

    /**
     * @param string|null $field
     * @return array|bool
     */
    public function getErrors(string $field = null): array|bool
    {
        if ($field) {
            return $this->validator->errors($field);
        }
        return $this->validator->errors();
    }
}
