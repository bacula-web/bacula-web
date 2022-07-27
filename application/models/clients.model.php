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

class Clients_Model extends CModel
{

    // ==================================================================================
    // Function: 	   count()
    // Parameters:	$tablename - Client table name
    // Return:		   Number of clients
    // ==================================================================================

    public function count($tablename = 'Client', $filter = null)
    {
        return parent::count($tablename);
    }

    // ==================================================================================
    // Function: 	getClients()
    // Parameters:	$pdo_connection - valide pdo object
    // Return:		array containing client list or false
    // ==================================================================================

    public function getClients()
    {
        $clients      = array();
        $table         = 'Client';
        $fields        = array('ClientId, Name');
        $orderby    = 'Name';

        $statment     = array( 'table' => $table, 'fields' => $fields, 'orderby' => $orderby );

        if (FileConfig::get_Value('show_inactive_clients') != null) {
            $statment['where'] = "FileRetention > '0' AND JobRetention > '0' ";
        }

        $result     = $this->run_query(CDBQuery::get_Select($statment));
            
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

    public function getClientInfos($client_id)
    {
        $client     = array();
        $fields     = array('name','uname');

        // Filter by clientid
        $this->addParameter('clientid', $client_id);
        $where[]    = 'clientid = :clientid';

        $statment   = CDBQuery::get_Select(array(   'table'=> 'Client', 
                                                    'fields' => $fields, 
                                                    'where' => $where ), $this->get_driver_name());
        
        $result     = $this->run_query($statment);
            
        foreach ($result->fetchAll() as $client) {
            $uname = explode(',', $client['uname']);

            // Check if client uname is not empty
            if (!empty($uname[0])) {
                // Windows Bacula file daemon
                if (end($uname) == 'Win32' || end($uname) == 'Win64') {
                    $client['arch'] = $uname[1];
                    $uname = explode(' ', $uname[0]);
                    $client['version'] = $uname[0];
                    $uname = array_slice($uname, 2);
                    $client['os'] = implode(' ', $uname);
                } else {
                    $uname = explode(' ', $client['uname']);
                    $client['version'] = $uname[0];
                    $uname = explode(',', $uname[2]);
                    $temp = explode('-', $uname[0]);
                    $client['arch'] = $temp[0];
                    $client['os'] = ucfirst($uname[1] . ' ' . $uname[2]);
                }
            } else {
                $client['version'] = 'n/a';
                $client['arch'] = 'n/a';
                $client['os'] = 'n/a';
            }
        }
        
        return $client;
    }
}
