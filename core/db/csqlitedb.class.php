<?php
/* 
+-------------------------------------------------------------------------+
| Copyright 2010-2012, Davide Franco			                          |
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

class CSqliteDB extends CDB {

  public function getSize() {
    $db_size = filesize($this->bwcfg->get_Catalog_Param($this->catalog_current_id, 'db_name') );
	return CUtils::Get_Human_Size($db_size);
  }

}
?>