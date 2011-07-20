<?php
/*
+-------------------------------------------------------------------------+
| Copyright 2010-2011, Davide Franco |
| |
| This program is free software; you can redistribute it and/or |
| modify it under the terms of the GNU General Public License |
| as published by the Free Software Foundation; either version 2 |
| of the License, or (at your option) any later version. |
| |
| This program is distributed in the hope that it will be useful, |
| but WITHOUT ANY WARRANTY; without even the implied warranty of |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the |
| GNU General Public License for more details. |
+-------------------------------------------------------------------------+
*/
	class CHttp
	{
		// Return a strip taged value
		private static function getSafeValue( $value )
		{
				return strip_tags($value);
		}

		// Return an array of $_POST or $_GET values
		// If $_POST or $_GET are empty, the return value is FALSE
		public static function getRequestVars( &$value )
		{
			$value_list = array();
			
			if( is_array( $value ) and count($value) > 0 ) {
					foreach( $value as $key => $var ) {
						if( isset($value[$key] ) )
							$value_list[$key] = self::getSafeValue( $var );
						else
							$value_list[$key] = false;
					}
			}else {
				return false;
			}

			return $value_list;
	}
} // end class

?>
