<?php

class CDBError extends Exception
{
	public function __construct( $message, $debug = false )
	{
		$this->debug_level 	= $debug;
		$this->message     	= $message;
		
		$this->raiseError();
		die();
	}
	
	public function raiseError()
	{
		echo '<h3>Dabase error: </h3>';
		echo 'Message: ' . $this->getMessage() . '<br />';
		echo 'Code: ' . $this->getCode()  . '<br />';
		echo 'Line: ' . $this->getLine()  . '<br />';
		echo 'File: ' . $this->getFile() . '<br />';
	}
}

?>