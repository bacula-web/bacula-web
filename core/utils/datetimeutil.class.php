<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco			                               | 
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

class DateTimeUtil
{

    // ==================================================================================
    // Function:        get_Timestamp()
    // Parameters:      $time (UNIX date string)
    // Return:          UNIX timestamp
    // ==================================================================================

    public static function get_Timestamp($time)
    {
        return strtotime($time);
    }

    // ==================================================================================
    // Function: 	    Get_Elapsed_Time()
    // Parameters:	    $start_time (start time in date format)
    //            	    $end_time (end time in date format)
    // Return:		    Job elapsed time (day) HH:MM:ss
    // ==================================================================================

    public static function Get_Elapsed_Time($start_time, $end_time)
    {
        $start = '';
        $end   = '';
	$elapsed_time = '';

        if ($start_time == '0000-00-00 00:00:00' or is_null($start_time) or $start_time == 0) {
            return 'n/a';
        } else {
            $start = self::get_Timestamp($start_time);
        }

        if ($end_time == '0000-00-00 00:00:00' or is_null($end_time) or $end_time == 0) {
            $end = mktime();
        } else {
            $end = self::get_Timestamp($end_time);
        }

        $diff = $end - $start;

        $daysDiff = sprintf("%02d", floor($diff / 60 / 60 / 24));
        $diff -= $daysDiff * 60 * 60 * 24;

        $hrsDiff = sprintf("%02d", floor($diff / 60 / 60));
        $diff -= $hrsDiff * 60 * 60;

        $minsDiff = sprintf("%02d", floor($diff / 60));
        $diff -= $minsDiff * 60;
        $secsDiff = sprintf("%02d", $diff);

	// If elapsed time is more than 24 hours, return a string with days
        if ($daysDiff > 0) {
            $elapsed_time = "$daysDiff day(s) $hrsDiff:$minsDiff$secsDiff";
        } else {
          $elapsed_time = "$hrsDiff:$minsDiff:$secsDiff";

          // Quick fix as Bacula has a bug with startdate and enddate when using pre nor post scripts
          if( $elapsed_time == "00:00:00" ) {
            $elapsed_time = "00:00:01";
          }
        }

  	return $elapsed_time;
    }

    // ==================================================================================
    // Function:        get_ElapsedSeconds()
    // Parameters:      $end
    //			        $start
    // Return:          amount of seconds between two UNIX date string or false
    // ==================================================================================

    public static function get_ElaspedSeconds($end, $start)
    {
        if (strtotime($start) && strtotime($end)) {
            $seconds = strtotime($end) - strtotime($start);

            // Quick fix as Bacula has a bug with startdate and enddate when using pre nor post scripts
	    if($seconds == 0) {
	      return 1;
            }else {
              return $seconds;
 	    }
        }else {
            return false;
        }
    }

    // ==================================================================================
    // Function: 	    get_Day_Intervals()
    // Parameters:	    $day
    // Return:		    array('start' => start_timestamp, 'end' => end_timestamp)
    // ==================================================================================

    public static function get_Day_Intervals($day)
    {
        $start = strtotime(date("Y-m-d 00:00:00", $day));
        $end   = strtotime(date("Y-m-d 23:59:59", $day));

        return array('start' => $start, 'end' => $end);
    }

    // ==================================================================================
    // Function: 	    getLastDaysIntervals()
    // Parameters:	    $nb_day
    // Return:		    array('start' => start_timestamp, 'end' => end_timestamp) of last n days
    // ==================================================================================

    public static function getLastDaysIntervals($nb_days)
    {
        $days = array();

        for ($d = $nb_days; $d >= 0; $d--) {
            $today  = NOW - ($d * DAY);
            $days[] = self::get_Day_Intervals($today);
        }
        return $days;
    }
}
