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
	static public function Get_Human_Size( $size, $decimal = 2, $unit = 'auto' )
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

class TimeUtils {
	static public function Get_Elapsed_Time( $start_time, $end_time)
	{
		$start = '';
		$end   = '';
		
		if( $start_time == '0000-00-00 00:00:00' )
			return 'N/A';
		else
			$start = strtotime( $start_time );					
        
		if( $end_time == '0000-00-00 00:00:00' )
			$end = mktime();
		else
			$end   = strtotime( $end_time );
		
		$diff = $end - $start;

        $daysDiff = sprintf("%02d", floor($diff/60/60/24) );
        $diff -= $daysDiff*60*60*24;

        $hrsDiff = sprintf("%02d", floor($diff/60/60) );
        $diff -= $hrsDiff*60*60;

        $minsDiff = sprintf("%02d", floor($diff/60) );
        $diff -= $minsDiff*60;
        $secsDiff = sprintf("%02d", $diff );

        if( $daysDiff > 0 )
			return $daysDiff . 'day(s) ' . $hrsDiff.':' . $minsDiff . ':' . $secsDiff;
        else
			return $hrsDiff . ':' . $minsDiff . ':' . $secsDiff;
	}
}

?>
