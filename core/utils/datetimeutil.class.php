<?php

/**
 * Copyright (C) 2010-2022 Davide Franco
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

namespace Core\Utils;

use DateTime;

class DateTimeUtil
{
    /*
     * Function:      checkDate()
     *
     * Parameter(s):  $date (date in string format)
     * Return:        true if $date is a valid date, else otherwise
     *
     */

    public static function checkDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if ($d != false) {
            if ($d->format('Y-m-d H:i:s') === $date) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * Return elapsed time for a job
     * @param string $start_time
     * @param string $end_time
     * @return string
     */

    public static function Get_Elapsed_Time($start_time, $end_time)
    {
        $dateInputFormat = 'Y-m-d H:i:s';

        if ($start_time == '0000-00-00 00:00:00' || is_null($start_time) || $start_time == 0) {
            return 'n/a';
        } else {
            $start = DateTime::createFromFormat($dateInputFormat, $start_time);
        }

        if ($end_time == '0000-00-00 00:00:00' || is_null($end_time) || $end_time == 0) {
            $end = new DateTime();
        } else {
            $end = DateTime::createFromFormat($dateInputFormat, $end_time);
        }

        $diff = $start->diff($end);

        if ($diff->d > 0) {
            return $diff->format('%d day(s), %H:%I:%S');
        } else {
            return $diff->format('%H:%I:%S');
        }
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
            if ($seconds == 0) {
                return 1;
            } else {
                return $seconds;
            }
        } else {
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
