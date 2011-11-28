<?php

class CDBResult extends PDOStatement
{
    protected function __construct()
    {
		$this->setFetchMode( PDO::FETCH_ASSOC );
	}
}

?>