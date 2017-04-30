<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco                                      |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
*/

class CTranslation
{

    private $language;
    private $charset;
    private $locale_path;
    private $lang_file;
    private $domaine;

    public function __construct($lang = 'en_EN')
    {
        $this->language     = $lang;
        $this->charset      = 'UTF-8';
        $this->domaine      = 'messages';
        $this->locale_path  = LOCALE_DIR;
    }

    public function set_Language(&$template)
    {
        // Template engine block registration
        $template->register_block('t', 'smarty_translate');

        // Setting up language
        putenv("LANGUAGE=" . $this->language . '.' . $this->charset);
        putenv("LANG=" . $this->language . '.' . $this->charset);
        setlocale(LC_ALL, $this->language . '.' . $this->charset);

        bindtextdomain($this->domaine, $this->locale_path);
        bind_textdomain_codeset($this->domaine, $this->charset);
        textdomain($this->domaine);
    }
}
