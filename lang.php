<?
/* 
+-------------------------------------------------------------------------+
| Copyright (C) 2004-2005 Juan Luis Frances Jiminez                       |
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
// begin Gettext initialization
// and we check if it is present at the system

if ( function_exists("gettext") ) {
        require($smarty_gettext_path."smarty_gettext.php");     
        $smarty->register_block('t','smarty_translate');
        
        $vars = $smarty->get_config_vars();
        $language = $vars['lang'];
        $domain = "messages";   
        putenv("LANG=$language"); 
        setlocale(LC_ALL, $language);
        bindtextdomain($domain,"./locale");
        textdomain($domain);
}
else {
        function smarty_translate($params, $text, &$smarty) {
                return $text;
        }
        $smarty->register_block('t','smarty_translate');
}
?>
