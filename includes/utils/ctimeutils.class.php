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

class CTimeUtils {
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
	
	static public function get_Day_Intervals( $day )
	{
		$start 			= strtotime( date( "Y-m-d 00:00:00", $day) );
		$end   			= strtotime( date( "Y-m-d 23:59:59", $day) );
		
		return array( 'start' => $start, 'end' => $end);
	}
	
	static public function getLastDaysIntervals( $nb_days )
	{
		$days = array();
		
		for( $d = $nb_days ; $d >= 0 ; $d-- ) {
			$today  = NOW - ($d * DAY);
			$days[] = self::get_Day_Intervals($today);
		}
		return $days;
	}
}

?>
