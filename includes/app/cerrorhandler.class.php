<?php
/*
+-------------------------------------------------------------------------+
| Copyright 2010-2011, Davide Franco                                              |
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

class CErrorHandler extends Exception
{
	private $header;
	private $debug;
	
	// Define Header
	public function setHeader( $header )
	{
		$this->header = $header;
	}
	
	// Enable debug mode
	public function setDebug( $debug = true )
	{
		$this->debug = $debug;
	}
	
	public function raiseError()
	{
		// Display error
		echo '<h3 style="background-color: #F0F0F0; width: 550px; padding: 5px; font-family: Arial,Verdana;">';
		
		if( !empty( $this->header ) )
			echo $this->header;
		else
			echo 'Application error';
		
		echo '</h3>';
		
		// Show more information if debug mode is enabled
		echo '<p style="width: 550px; padding: 5px; font-family: Arial,Verdana; font-size: 10pt;">';
		echo 'Message: ' . $this->getMessage() . '<br />';
		if( $this->debug ) {
			echo 'Code: ' . $this->getCode()  . '<br />';	
			echo 'Line: ' . $this->getLine()  . '<br />';
			echo 'File: ' . $this->getFile() . '</p>';
		}
		
		// Display footer
		$footer  = '<p style="font-size: 10pt; background-color: #F0F0F0; width: 550px; padding: 5px; font-family: Arial,Verdana;">';
		$footer .= 'Tried to run the <a href="test.php">test page</a> ?<br />';
		$footer .= 'Read the documentation on the <a href="http://bacula-web.dflc.ch" target="_blank">Bacula-Web project site</a> <br />';
		$footer .= 'Rebort a bug or suggest a new feature in the <a href="http://bacula-web.dflc.ch/bugs" target="_blank">Bacula-Web\'s bugtracking tool</a> <br /> </p>';
		echo $footer;
		
		die();
	}
}

?>

