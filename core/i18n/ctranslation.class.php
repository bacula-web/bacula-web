<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2023 Davide Franco
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

namespace Core\i18n;

class CTranslation
{
    /**
     * @var string
     */
    private string $language;

    /**
     * @var string
     */
    private string $charset;

    /**
     * @var string
     */
    private string $localePath;

    /**
     * @var string
     */
    private string $domain;

    /**
     * @param string $lang
     */
    public function __construct(string $lang = 'en_EN')
    {
        $this->language = $lang;
        $this->charset  = 'UTF-8';
        $this->domain  = 'messages';
        $this->localePath  = LOCALE_DIR;
    }

    /**
     * @return void
     */
    public function setLanguage(): void
    {
        putenv('LANGUAGE=' . $this->language . '.' . $this->charset);
        putenv('LANG=' . $this->language . '.' . $this->charset);
        setlocale(LC_ALL, $this->language . '.' . $this->charset);

        bindtextdomain($this->domain, $this->localePath);
        bind_textdomain_codeset($this->domain, $this->charset);
        textdomain($this->domain);
    }
}
