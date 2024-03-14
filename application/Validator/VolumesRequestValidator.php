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

class VolumesRequestValidator extends AbstractValidator
{
    public function __construct(array $parameters)
    {
        parent::__construct($parameters, [
            'page',
            'filter_pool_id',
            'filter_orderby',
            'filter_orderby_asc',
            'filter_inchanger'
        ]);

        $this->setRules();
    }

    public function setRules(): void
    {
        $this->validator->rule('integer', ['page']);
        $this->validator->rule('integer', ['filter_pool_id']);
        $this->validator->rule('in', 'filter_orderby', [
            'Name' => 'Name',
            'MediaId' => 'Id',
            'VolBytes' => 'Bytes',
            'VolJobs' => 'Jobs'
        ]);
        $this->validator->rule('in', 'filter_orderby_asc', ['ASC', 'DESC']);
    }
}
