<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright 2010-2017, Davide Franco			                    	         |
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
 
 // Time intervals in secondes
 define('FIRST_DAY', mktime(0, 0, 0, 1, 1, 1970));
 define('DAY', 86400);
 define('WEEK', 7 * DAY);
 define('MONTH', 4 * WEEK);
 
 // Timestamp constants
 define('NOW', CDB::getServerTimestamp());
 define('LAST_DAY', NOW - DAY);
 define('LAST_WEEK', NOW - (7 * DAY));
 define('LAST_MONTH', NOW - (4* WEEK));

 // Job status code
 define('J_NOT_RUNNING', 'C');
 define('J_RUNNING', 'R');
 define('J_BLOCKED', 'B');
 define('J_COMPLETED', 'T');
 define('J_COMPLETED_ERROR', 'E');
 define('J_NO_FATAL_ERROR', 'e');
 define('J_FATAL', 'f');
 define('J_CANCELED', 'A');
 define('J_WAITING_CLIENT', 'F');
 define('J_WAITING_SD', 'S');
 define('J_WAITING_NEW_MEDIA', 'm');
 define('J_WAITING_MOUNT_MEDIA', 'M');
 define('J_WAITING_STORAGE_RES', 's');
 define('J_WAITING_JOB_RES', 'j');
 define('J_WAITING_CLIENT_RES', 'c');
 define('J_WAITING_MAX_JOBS', 'd');
 define('J_WAITING_START_TIME', 't');
 define('J_WAITING_HIGH_PR_JOB', 'p');

 // Job levels
 define('J_FULL', 'F');
 define('J_DIFF', 'D');
 define('J_INCR', 'I');
