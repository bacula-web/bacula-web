<?php

class CDBError
{
	static public function raiseError( $exception )
	{
		echo '<h3>Dabase error: </h3>';
		echo 'Message: ' . $exception->getMessage() . '<br />';
		echo 'Code: ' . $exception->getCode()  . '<br />';
		echo 'Line: ' . $exception->getLine()  . '<br />';
		echo 'File: ' . $exception->getFile() . '<br />';
	}
}

?>