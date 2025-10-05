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

namespace App\Table;

use Core\Db\Table;

class UserTable extends Table
{
    protected ?string $tablename = 'Users';

    /**
     * @return bool|int
     */
    public function createSchema(): bool|int
    {
        $createSchemaQuery = 'CREATE TABLE IF NOT EXISTS Users (
                        user_id INTEGER PRIMARY KEY,
                        username TEXT NOT NULL UNIQUE,
                        passwordHash TEXT NOT NULL,
                        email TEXT
                        );
                        CREATE INDEX IF NOT EXISTS User_ix_username ON Users (username);';

        return $this->pdo->exec($createSchemaQuery);
    }
}
