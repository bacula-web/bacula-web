<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2016, Davide Franco			                            |
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

class CUtils
{

    public static function Get_Human_Size($size, $decimal = 2, $unit = 'auto', $display_unit = true)
    {
        $unit_id = 0;
        $lisible = false;
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $hsize = $size;

        switch ($unit) {
            case 'auto';
                while (!$lisible) {
                    if ($hsize >= 1024) {
                        $hsize = $hsize / 1024;
                        $unit_id++;
                    } else {
                        $lisible = true;
                    }
                } // end while
                break;

            default:
                $exp = array_keys($units, $unit);
                $unit_id = current($exp);
                $hsize = $hsize / pow(1024, $unit_id);
                break;
        } // end switch
        // Format human readable value (with dot for decimal separator)
        $hsize = number_format($hsize, $decimal, '.', '');

        // Append unit or not
        if ($display_unit) {
            $hsize = $hsize . ' ' . $units[$unit_id];
        }

        return $hsize;
    }
    
    // ==================================================================================
    // Function: 	format_Number()
    // Parameters:	$number
    //				$decimal (optional, default = 0)
    // Return:		formated number depending on current locale
    // ==================================================================================
    public static function format_Number($number, $decimal = 0)
    {
        // Getting localized numeric formating information
        $locale = localeconv();

        // Return formated number
        return number_format($number, $decimal, $locale['decimal_point'], $locale['thousands_sep']);
    }

    // ==================================================================================
    // Function: 	format_Date()
    // Parameters:	$date
    //				$format (optional, default = null)
    // Return:		formated date
    // ==================================================================================
    public static function format_Date($date, $format = null)
    {
        // Return formated date
        if($format == null) {
           return date('Y-m-d', strtotime($date));
        } else {
             return date($format, strtotime($date) );
        }
    }

    // ==================================================================================
    // Function: 	format_DateTime()
    // Parameters:	$date
    //				$format (optional, default = null)
    // Return:		formated date
    // ==================================================================================
    public static function format_DateTime($date, $format = null)
    {
        // Return formated date
        if($format == null) {
           return date('Y-m-d H:i:s', strtotime($date));
        } else {
             return date($format, strtotime($date) );
        }
    }
//$this->escape(date($this->datetime_format,strtotime($line['last_login'])));?

}
