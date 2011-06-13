<?php
/* 
+-------------------------------------------------------------------------+
| Copyright 2010-2011, Davide Franco			                          |
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
class Utils {
	public function Get_Human_Size( $size, $decimal = 2, $unit = 'auto' )
	{
		$unit_id = 0;
		$lisible = false;
		$units = array('B','KB','MB','GB','TB');
		$hsize = $size;

		switch( $unit )
		{
			case 'auto';
				while( !$lisible ) {
					if ( $hsize >= 1024 ) {
						$hsize    = $hsize / 1024;
						$unit_id += 1;
					}	 
					else
						$lisible = true;
				} // end while
			break;
			
			default:
				$p = array_search( $unit, $units);
				$hsize = $hsize / pow(1024,$p);
			break;
		} // end switch
		
		$hsize = sprintf("%." . $decimal . "f", $hsize);
		$hsize = $hsize . ' ' . $units[$unit_id];
		return $hsize;
	}
}
?>