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

class Clients_Model extends CModel
{

    // ==================================================================================
    // Function: 	count()
    // Parameters:	$pdo_connection - valide pdo object
    // Return:		Number of clients
    // ==================================================================================

    public static function count($pdo, $tablename = 'Client', $filter = null)
    {
        return CModel::count($pdo, $tablename);
    }

    // ==================================================================================
    // Function: 	getClients()
    // Parameters:	$pdo_connection - valide pdo object
    // Return:		array containing client list or false
    // ==================================================================================

    public static function getClients($pdo)
    {
        $clients      = array();
        $table         = 'Client';
        $fields        = array('ClientId, Name');
        $orderby    = 'Name';

        $statment     = array( 'table' => $table, 'fields' => $fields, 'orderby' => $orderby );

        if (FileConfig::get_Value('show_inactive_clients')) {
            $statment['where'] = "FileRetention > '0' AND JobRetention > '0' ";
        }

        $result     = CDBUtils::runQuery(CDBQuery::get_Select($statment), $pdo);
            
        foreach ($result->fetchAll() as $client) {
            $clients[ $client['clientid'] ] = $client['name'];
        }

        return $clients;
    }
    
    // ==================================================================================
    // Function: 	getClientInfos()
    // Parameters: 	client id
    // Return:		array containing client information
    // ==================================================================================

    public static function getClientInfos($pdo, $client_id)
    {
        $client     = array();
        $fields     = array('name','uname');
        $where      = array( "clientid = $client_id" );
        $statment   = CDBQuery::get_Select(array('table'=> 'Client', 'fields' => $fields, 'where' => $where ));
        
        $result     = CDBUtils::runQuery($statment, $pdo);
            
        foreach ($result->fetchAll() as $client) {
            $uname              = explode(' ', $client['uname']);
            $client['version']  = $uname[0];
            $uname              = explode(',', $uname[2]);
            $temp               = explode('-', $uname[0]);
            $client['arch']     = $temp[0];
            $client['os']       = $uname[1];
        }
        
        return $client;
    }
}
