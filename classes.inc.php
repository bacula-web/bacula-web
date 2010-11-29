<?php
/* 
+-------------------------------------------------------------------------+
| Copyright (C) 2004-2005 Juan Luis Frances Jiminez                       |
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
// Last Err: 11
define('CONFIG_DIR', "configs");
define('CONFIG_FILE', "bacula.conf");
define('BACULA_TYPE_BYTES_FILES', 1);
define('BACULA_TYPE_FILES_JOBID', 2);
define('BACULA_TYPE_BYTES_ENDTIME_ALLJOBS', 69);

require_once "paths.php";
require_once "DB.php";                                                                                                                  // Pear DB
require_once "config.inc.php";
require_once($smarty_path."Config_File.class.php");

if (!function_exists('array_fill')) {                                                                                   // For PHP < 4.2.0 users 
    require_once('array_fill.func.php');
}

class Bweb extends DB {

    var $StartDate;
    var $EndDate;
    var $driver;
	var $dbs;
	var $dbs_name;
	
	public $db_link;						// Database link
	private $db_dsn;						// Data Source Name
	
	private $config_file;					// Config filename
	private $config;						// Loaded config from bacula.conf
	private $catalogs = array();			// Catalog array

    function __construct()
	{             
		$this->catalogs = array();
		
		// Loading configuration
		$this->config_file = getcwd() . '/configs/bacula.conf';
		if( !$this->load_config() )
			die( "Unable to load configuration");
			
		//echo "Number of catalog defined " . count($this->catalogs) . "<br />";
		
		/*
		$conf = new Config_File (CONFIG_DIR);
		$this->dbs = array();

		$i = 2;
		$sections = $conf->get(CONFIG_FILE,"DATABASE","host");
		array_push($this->dbs, "DATABASE");

		while ( !empty($sections) ) {                
			$sections = $conf->get(CONFIG_FILE,"DATABASE".$i,"host");
			if ( !empty($sections) )
				array_push($this->dbs,"DATABASE".$i);
			$i++;
		}

		if ( $i < 4)
			$sec = "DATABASE";
		else {
			if ( !empty($_POST['sel_database']) ) {
				$_SESSION['DATABASE'] = $_POST['sel_database'];
				$sec = $_POST['sel_database'];
			} else {
				if (isset($_SESSION['DATABASE']) )
					$sec = $_SESSION['DATABASE'];
				else
					$sec = "DATABASE";
			}
		}

        $this->dsn['hostspec'] = $conf->get(CONFIG_FILE,$sec,"host");
        $this->dsn['username'] = $conf->get(CONFIG_FILE,$sec,"login");
        $this->dsn['password'] = $conf->get(CONFIG_FILE,$sec,"pass");
        $this->dsn['database'] = $conf->get(CONFIG_FILE,$sec,"db_name");
        $this->dsn['phptype']  = $conf->get(CONFIG_FILE,$sec,"db_type");   // mysql, pgsql
        
		if (  $conf->get(CONFIG_FILE,$sec,"db_port") )
			$this->dsn[port] = $conf->get(CONFIG_FILE,$sec,"db_port");
		*/
		
		// Construct a valid dsn
        $this->db_dsn['hostspec'] = $this->catalogs[0]["host"];
        $this->db_dsn['username'] = $this->catalogs[0]["login"];
		$this->db_dsn['password'] = $this->catalogs[0]["pass"];
		$this->db_dsn['database'] = $this->catalogs[0]["db_name"];
		$this->db_dsn['phptype']  = $this->catalogs[0]["db_type"];
		
		                        
        $this->db_link = $this->connect($this->db_dsn);
        
		if (DB::isError($this->db_link)) {
			die($this->db_link->getMessage());
		}else {
			$this->driver = $this->db_dsn['phptype'];                            
            register_shutdown_function(array(&$this,'close'));
			$this->dbs_name = $this->db_dsn['database'];
		}
	}
                
    function load_config()
	{
		$this->config = parse_ini_file( $this->config_file, true );
		
		if( !$this->config == false ) {
			// Loading database connection information
			foreach( $this->config as $parameter => $value )
			{
				//echo "Param $parameter = $value <br />";
				if( is_array($value) ){		// Parsing database section
					array_push( $this->catalogs, $value );
				}
			}
			return true;
		}else
			return false;
	}
	
	public function get_config_param( $param )
	{
		if( isset( $this->config[$param] ) )
			return $this->config[$param];
		else
			return false;
	}
	
	public function Get_Nb_Catalogs() 
	{
		return count( $this->catalogs );
	}
	
	
	function close() 
	{
		$this->db_link->disconnect();
    }      

        
         
        function CalculateBytesPeriod($server,$StartDate,$EndPeriod) {   // Bytes transferred in a period.

                $result =& $this->db_link->query("select SUM(JobBytes) from Job WHERE EndTime < '$EndPeriod' and EndTime > '$StartDate' and Name='$server'")
                        or die("classes.inc: Error query: 1");
                $return =& $result->fetchRow(); 
                return $return[0];
        }//end function

        
         
        function CalculateFilesPeriod($server,$StartDate,$EndPeriod) {    // Number of files transferred in a period.

                $result =& $this->db_link->query("select SUM(JobFiles) from Job WHERE EndTime < '$EndPeriod' and EndTime > '$StartDate' and Name='$server'")
                        or die("classes.inc: Error query: 2");
                $return =& $result->fetchRow();
                return $return[0];
        }//end function 

                 

        function PrepareDate($StartDateMonth,$StartDateDay,$StartDateYear,$EndDateMonth,$EndDateDay,$EndDateYear) {  // Convert date for Smarty. Check if only works with Mysql.
        
                $this->StartDate=$StartDateYear."-".$StartDateMonth."-".$StartDateDay." 00:00:00";
                $this->EndDate=$EndDateYear."-".$EndDateMonth."-".$EndDateDay." 23:59:59";  // last day full
                
        }//end function
 
        function GetDataVolumes() {

                $volume = array();
                $res = $this->db_link->query("SELECT Name FROM Pool");
                while ( $tmp =& $res->fetchRow() ) {
                        if ($this->driver == "mysql" )
                                $result = $this->db_link->query("select Media.VolumeName, Media.VolBytes,Media.VolStatus,Pool.Name,Media.MediaType,Media.LastWritten,FROM_UNIXTIME(UNIX_TIMESTAMP(Media.LastWritten)+Media.VolRetention ) as expire from Pool LEFT JOIN Media ON Media.PoolId=Pool.PoolId where Name='$tmp[0]' order by Media.VolumeName");
                        else if ($this->driver == "pgsql")
				$result = $this->db_link->db_query("select Media.VolumeName, Media.VolBytes,Media.VolStatus,Pool.Name,Media.MediaType,Media.LastWritten, Media.LastWritten + Media.VolRetention * interval '1 second' as expire from Pool LEFT JOIN Media ON Media.PoolId=Pool.PoolId where Name='$tmp[0]' order by Media.VolumeName");
                        while ( $tmp1 = $result->fetchRow() ) {
                                $pos = array_key_exists($tmp[0],$volume);
                                if ($pos != FALSE)
                                        array_push($volume["$tmp[0]"],$tmp1);
                                else
                                        $volume += array($tmp[0]=>array($tmp1));
                        }
                }
                
                $res->free();
                $result->free();
                return $volume;
        }
        
		function human_file_size( $size, $decimal = 2 )
		{
			$unit_id = 0;
			$lisible = false;
			$units = array('B','KB','MB','GB','TB');
			$hsize = $size;
				
			while( !$lisible ) {
				if ( $hsize >= 1024 ) {
					$hsize    = $hsize / 1024;
					$unit_id += 1;
				} 
				else {
					$lisible = true;
				} 
			} 
			// Format human size
			$hsize = sprintf("%." . $decimal . "f", $hsize);
			return $hsize . ' ' . $units[$unit_id];
		} // end function

		
		function GetDbSize() 
		{
			$database_size = 0;
			if ( $this->driver == "mysql") {
				$dbsize = $this->db_link->query("show table status") or die ("classes.inc: Error query: 3");
				
				if ( $dbsize->numRows() ) {
					while ( $res = $dbsize->fetchRow(DB_FETCHMODE_ASSOC) )
						$database_size += $res["Data_length"];
                } else {
					return 0;
				} // end if else
            } // end if
            else if ( $this->driver == "pgsql") {
				$dbsize = $this->db_link->query("select pg_database_size('$this->dbs_name')") or die ("classes.inc: Error query: 4");
				
				if (PEAR::isError($dbsize))
					die($dbsize->getMessage());
                    
				if ( $dbsize->numRows() ) {
					while ( $res = $dbsize->fetchRow() )
						$database_size += $res[0];
                } else {
					return 0;
				}
            } // end if       
				
			$dbsize->free();
		
			return $this->human_file_size( $database_size );  
        } // end function GetDbSize()
		
		public function Get_Nb_Clients()
		{
			$clients = $this->db_link->query("SELECT COUNT(*) AS nb_client FROM Client");
			if( PEAR::isError($clients) )
				die( "Unable to get client number" );
			else
				return $clients->fetchRow( DB_FETCHMODE_ASSOC );
		}

		// Return an array of volumes ordered by poolid and volume name
		function GetVolumeList() {

                $volumes   = array();
                $query     = "";
				$debug	   = false;
				
				// Get the list of pools id
				$query = "SELECT Pool.poolid, Pool.name FROM Pool ORDER BY Pool.poolid";
				
				$this->db_link->setFetchMode(DB_FETCHMODE_ASSOC);
				$pools = $this->db_link->query( $query );
				
				if( PEAR::isError( $pools ) )
					die("Error: Failed to get pool list <br />SQL Query: $query<br />" . $pools->getMessage() );
				
				while( $pool = $pools->fetchRow() ) {
					switch( $this->driver )
					{
						case 'mysql':
/*
							$query  = "SELECT Media.VolumeName, Media.VolBytes, Media.VolStatus, Pool.Name, Media.MediaType,Media.LastWritten, FROM_UNIXTIME(UNIX_TIMESTAMP(Media.LastWritten)+Media.VolRetention ) AS expire 
									   FROM Pool LEFT JOIN Media ON Media.PoolId=Pool.PoolId WHERE poolid='$pool[0]' 
									   ORDER BY Media.VolumeName";
*/
							$query  = "SELECT Media.volumename, Media.volbytes, Media.volstatus, Media.mediatype, Media.lastwritten, Media.volretention
									   FROM Media LEFT JOIN Pool ON Media.poolid = Pool.poolid
								       WHERE Media.poolid = '". $pool['poolid'] . "' ORDER BY Media.volumename";
						break;
						case 'pgsql':
							$query  = "SELECT media.volumename, media.volbytes, media.volstatus, media.mediatype, media.lastwritten, media.volretention
									   FROM media LEFT JOIN pool ON media.poolid = pool.poolid
								       WHERE media.poolid = '". $pool['poolid'] . "' ORDER BY media.volumename";
							/*
							$query  = "SELECT Media.VolumeName, Media.VolBytes,Media.VolStatus,Pool.Name,Media.MediaType,Media.LastWritten, Media.LastWritten + Media.VolRetention * interval '1 second' AS expire 
									   FROM Pool LEFT JOIN Media ON media.poolid=pool.poolid WHERE poolid='$pool[0]' 
									   ORDER BY Media.VolumeName";
							*/
						break;
						case 'sqlite':
							$query  = "";		// not yet implemented
						break;
						default:
						break;
					} // end switch
					
					$this->db_link->setFetchMode(DB_FETCHMODE_ASSOC);
					$medias = $this->db_link->query( $query );

					if( PEAR::isError( $medias ) ) {
						die( "Failed to get media list for pool $volume[0] <br /> " . $medias->getMessage() );
					}else {
						if( $debug ) echo "Found " . $medias->numRows() . " medias for pool " . $pool['name'] . " <br />";
					
						// Create array key for each pool
						if( !array_key_exists( $pool['name'], $volumes) )
						{
							$volumes[ $pool['name'] ] = array();
						}
						while( $media = $medias->fetchRow() ) {
							if( $debug ) {
								var_dump( $media );
							}

							if( $medias->numRows() == 0 ) {
								if( $debug ) echo "No media in pool " . $pool['name'] . "<br />";
							} else {
									if( $media['lastwritten'] != "0000-00-00 00:00:00" ) {
										// Calculate expiration date
										$expire_date     = strtotime($media['lastwritten']) + $media['volretention'];
										$media['expire'] = strftime("%Y-%M-%D", $expire_date);
										
										// Media used size in a more readable format
										$media['volbytes'] = $this->human_file_size( $media['volbytes'] );
									} else {
										$media['lastwritten'] = "N/A";
										$media['expire']      = "N/A";
										$media['volbytes'] 	  = "0 KB";
									}								
								// Add the media in pool array
								array_push( $volumes[ $pool['name']], $media);
							}
						} // end while
					} // end if else
				} // end while
				return $volumes;
        } // end function GetVolumeList()
		
		public function GetLastJobs( $delay = LAST_DAY )
		{
			$query 		= "";
			$start_date = "";
			$end_date 	= "";
			
			// Interval calculation
			$end_date   = mktime();
			$start_date = $end_date - $delay;
			
			$start_date = date( "Y-m-d H:m:s", $start_date );
			$end_date   = date( "Y-m-d H:m:s", $end_date );
			
			switch( $this->driver )
			{
				case 'mysql':
					$query  = 'SELECT COUNT(JobId) AS completed_jobs ';
					$query .= 'FROM Job ';
					$query .= "WHERE EndTime BETWEEN '$start_date' AND '$end_date' ";
					$query .= "AND JobStatus = 'T'";
				break;
			}
		
			$jobs = $this->db_link->query( $query );
		
			if (PEAR::isError( $lastjobstatus ) ) {
				die( "Unable to get last completed jobs status from catalog<br />" . $status->getMessage() );
			}else {
				return $jobs->fetchRow();
			}
		} // end function GetLastJobStatus()
		
		public function GetLastErrorJobs( $delay = LAST_DAY )
		{
			$query 		= "";
			$start_date = "";
			$end_date 	= "";
			
			// Interval calculation
			$end_date   = mktime();
			$start_date = $end_date - $delay;
			
			$start_date = date( "Y-m-d H:m:s", $start_date );
			$end_date   = date( "Y-m-d H:m:s", $end_date );
			
			//echo "start date: $start_date <br />";
			//echo "end date: $end_date <br />";
			
			switch( $this->driver )
			{
				default:
					$query  = 'SELECT COUNT(JobId) AS failed_jobs ';
					$query .= 'FROM Job ';
					$query .= "WHERE EndTime BETWEEN '$start_date' AND '$end_date' ";
					$query .= "AND JobStatus = 'f'";
				break;
			}				
			$result = $this->db_link->query( $query );
			
			if (PEAR::isError( $result ) ) {
				die( "Unable to get last failed jobs status from catalog<br />query = $query <br />" . $result->getMessage() );
			}else {
				return $result->fetchRow( DB_FETCHMODE_ASSOC );
			} // end if else
		} // end function GetLastErrorJobs
		
		public function Get_BackupJob_Names()
		{
			$query 		= "SELECT Name FROM Job GROUP BY Name";
			$backupjobs = array();
			
			$result = $this->db_link->query( $query );
			
			if (PEAR::isError( $result ) ) {
				die("Unable to get BackupJobs list from catalog" );
			}else{
				while( $backupjob = $result->fetchRow( DB_FETCHMODE_ASSOC ) ) {
					array_push( $backupjobs, $backupjob["Name"] );
				}
				return $backupjobs;
			}
		}
		
} // end class Bweb

class BGraph {

        var $type;
        var $sizex;
        var $sizey;
        var $MarginBottom;
        var $MarginLeft;
        var $Leg;


        
        function BShowGraph($datos,$title,$xlabel,$ylabel,$leyenda,$tipo="lines") {
        
                global $type;
        
                require_once ("external_packages/phplot/phplot.php");

                if ( empty($this->sizex) || empty($this->sizey) ) {    //Default size
                        $this->sizex = "600";
                        $this->sizey = "400";
                }
                if ( empty($this->MarginBottom) ) {
                        $this->MarginBottom = 120;
                }
                
                $legend = $leyenda;
//              $bgcolor = array(222,206,215);      // Background color of graph
                $bgcolor = array(207,231,231);
                $fgcolor = array(110,41,57);
                

                
                $graph = new PHPlot($this->sizex,$this->sizey,"","");

                if ( !empty($type) )
                        $graph->setDataType($type);

                $graph->SetDataValues($datos);
                $graph->SetPlotType($tipo);
//              $graph->SetUseTTF(1);
                $graph->SetBackgroundColor($bgcolor);

                $graph->SetLegendPixels(1,20);
                $graph->SetDataColors(array('SkyBlue','purple','PeachPuff','aquamarine1','#2CB04B','beige','#9F865F','#135568','orchid','navy','red', 'black', 'blue', 'green', 'brown', 'yellow','cyan','orange','#B9F5A7','#AFAFAF'));
                $graph->SetTitle($title);
                $graph->SetXLabel($xlabel);
                $graph->SetYLabel($ylabel);
                $graph->SetPlotAreaWorld("","","","");
                
                if ( count($datos) > 5 )
                        $graph->SetXDataLabelAngle(90);
                else
                        $graph->SetXDataLabelAngle(0);
		$graph->SetNumXTicks(1);
//              $graph->SetXDataLabelPos('none');
//              $graph->SetXTickLabelPos('plotdown');
                
//              $graph->SetXGridLabelType("time");
//              $graph->SetXTimeFormat("%b ") ;

                if ( $this->Leg == 1 ) {
                        $this->MarginLeftWithLegend($legend);
                        $graph->SetMarginsPixels($this->MarginLeft,10,35,$this->MarginBottom);
                        $graph->SetLegend($legend);
				}
                else
                        $graph->SetMarginsPixels(90,35,35,$this->MarginBottom);
//              $graph->SetDataColors(array($fgcolor),array( "black"));
                $graph->SetFileFormat( "png");
//              $graph->DoScaleData(1,1);
//              $graph->DoMovingAverage(1,1,1);

//              FIX ME -- to round y axis.
                $vtick = strlen (round ($graph->max_y));
                $res = 1;
                for ($i=1;$i < $vtick; $i++)
                        $res = $res*10;
                if (strlen($graph->max_y-$res) != $vtick )
                        $res = $res/10;
                $graph->SetVertTickIncrement($res);
                $graph->DrawGraph();

        }//end Crear


//Estupidez que tengo que cambiar. !!!!!!!!!!!
        function SetDataType($typ) {
                
                global $type;
                $type = $typ;
        }

        function MarginLeftWithLegend($clients) {
                
                $maxlen = 0;
                
                while (next($clients)) {
                        $tmp = strlen(current($clients));
                        if ( $tmp > $maxlen )
                                $maxlen = $tmp;
                }
                $this->MarginLeft = $maxlen * 9;
        }       

}//end class





class BCreateGraph extends BGraph {

        var $BD_bacula;
        var $izquierda;
        var $derecha;
        var $StartDate;
        var $EndDate;
        var $elapsed;                        // Default elapsed time to show complex graphs
        
        
        
        function BCreateGraph() {
        
                $this->StartDate = "1900-01-01";
                $this->EndDate = "4000-01-01";
                $this->elapsed = "86400";                   // 24 hours in seconds.
                
         }              
         
         
         
        function BCreate($server,$tipo_dato,$title,$tipo="bars",$xlabel="",$ylabel="") {
        
                global $DB_bacula;
                global $izquierda;
                global $derecha;
                global $clientes;
        
                $this->clientes=array();
                $DB_bacula = new Bweb();
                $datos = $this->SQLPrepareData($server,$tipo_dato);
        
                if ( empty($datos) ) {                       //No data = No stats = Empty graph
                        header("Content-type: image/png");
                        $img= @ImageCreate(200,100) or die ("Cannot intialize GD stream");
                        $bgc= ImageColorAllocate($img, 0, 255,255);
                        $txc= ImageColorAllocate($img, 0,0,0);
                        ImageString($img, 5, 4, 4, "No data to process", $txc);
                        ImagePng($img);
                        ImageDestroy($img);
                        return; 
                }
        
                if ( empty ($xlabel) ) {                       // If no label, table names like leyends
                        $xlabel=$derecha; $ylabel=$izquierda; 
                } 
                        
                $this->SetDataType("text-data");
                $this->BShowGraph($datos,$title,$xlabel,$ylabel,$this->clientes,$tipo);
                
        }


 
        function SQLPrepareData($servidor,$tipo_dato=0) {         // Prepare bytes data from database.

                global $DB_bacula;
                global $izquierda;
                global $derecha;
        
                if ( $tipo_dato<30 ) {               // Simple graph. Only 2 data 
        
                switch ($tipo_dato)
                                {
                                case BACULA_TYPE_BYTES_FILES:
                                        $izquierda="jobbytes";
                                        $derecha="jobfiles";
                                        break;
                                case BACULA_TYPE_FILES_JOBID:
                                        $izquierda="jobfiles";
                                        $derecha="jobid";
                                        break;
                                default:
                                        $izquierda="jobbytes";
                                        $derecha="endtime";
                                        break;
                                }
                        $result = $DB_bacula->db_link->query("select $derecha,$izquierda from Job where Name='$servidor' and EndTime < '$this->EndDate' and EndTime > '$this->StartDate' order by SchedTime asc")
                                or die ("classes.inc: Error at query: 5");
                while ( $row = $result->fetchRow(DB_FETCHMODE_ASSOC) ) {
                        $whole_result[] = $this->array_merge_php4($row["$derecha"],$row[$izquierda]);
                }
                $result->free();
        } else {                                                // Complex graph. 3 or more data.
                
                        switch ( $tipo_dato )
                                {
                                case '30':                      // Unused, at this time.
                                        $result = $DB_bacula->db_link->query("select JobBytes,JobFiles,Jobid from Job where Name='$servidor' order by EndTime asc")
                                                or die ("classes.inc: Error at query: 6");
                                        while ( $row = $result->fetchRow(DB_FETCHMODE_ASSOC) )
                                                $whole_result[] = array_merge($row["Jobid"],$row["JobFiles"],$row["JobBytes"]);
                                        $result->free();
                                        break;
                                case BACULA_TYPE_BYTES_ENDTIME_ALLJOBS:  // Special: Generic graph from all clientes.
                                        $i = -1;                         // Counter of number of jobs of one client. SP: Contador del nmero de jobs totales de un cliente.
                                        $i2 = 0;                         // Counter of number of keys of array. SP: Contador del nmero de valores del array.
                                        
                                        if ($DB_bacula->driver == "mysql") {
                                        $res = $DB_bacula->db_link->query("select Name from Job where UNIX_TIMESTAMP(EndTime) > UNIX_TIMESTAMP(NOW())-$this->elapsed  group by Name order by Name desc")
                                                or die ("classes.inc: Error at query: 7");
                                                $resdata = $DB_bacula->db_link->query("select date_format(EndTime,\"%Y-%m-%d\") from Job where UNIX_TIMESTAMP(EndTime) > UNIX_TIMESTAMP(NOW())-$this->elapsed  group by date_format(EndTime, \"%Y-%m-%d\") order by EndTime")
                                                        or die ("classes.inc: Error at query: 8");
					}
                                        else if ($DB_bacula->driver == "pgsql") {
						$res = $DB_bacula->db_link->query("select Name from Job where EndTime > now() - 1*interval'$this->elapsed s'  group by Name order by Name desc")
							or die ("classes.inc: Error at query: 8");
                                                $resdata = $DB_bacula->db_link->query("select to_char(EndTime,'YY-MM-DD') from Job where EndTime > NOW() - 1*interval'$this->elapsed s'  group by EndTime order by EndTime")
                                                        or die ("classes.inc: Error at query: 9");
					}
                                        
					if (PEAR::isError($resdata))
						die("classes.inc: Error at query: 9.1<br>".$resdata->getMessage());
                                        while ( $tmpdata = $res->fetchRow() )
                                                array_push($this->clientes,$tmpdata[0]);
                                                
//                                      echo "<pre>";
//                                      print_r ($this->clientes);
//                                      echo "</pre>";
                                        
                                        
                                        $spr 			= array();                        // Temporal array
                                        $spr2 			= array();                       // Temporal array
                                        $whole_result 	= array();
                                        $count 			= 0;
										
                                        while ( $tmpdata = $resdata->fetchRow() ) {
                                                $count++;
                                                array_push($spr,$tmpdata[0]);
                                                if ($DB_bacula->driver == "mysql")
                                                        $result = $DB_bacula->db_link->query("select date_format(EndTime,\"%Y-%m-%d\"),SUM(JobBytes) as sum,Name as name,count(Name) as Nname from Job WHERE EndTime like '$tmpdata[0]%' group by Name order by Name desc")
                                                                or die ("classes.inc: Error at query: 10");
                                                else if ($DB_bacula->driver == "pgsql") {
							$query = "select to_char(EndTime,'YY-MM-DD'),SUM(JobBytes) as sum,Name,count(Name) as Nname from Job WHERE EndTime like '%$tmpdata[0]%' group by EndTime,Name order by Name desc";
                                                        $result = $DB_bacula->db_link->query($query)
                                                                or die ("classes.inc: Error at query: 11");
						}
                                                while ( $row = $result->fetchRow(DB_FETCHMODE_ASSOC) ) {
                                                        $spr2 = array_merge($spr2,array($row["name"]=>$row["sum"]));
                                                        $i = $result->numRows();
                                                }

                                        
//                                              echo "<pre>";
//                                              print_r ($spr2);
//                                              echo "</pre>";
                                                
                                                reset ($this->clientes);        
						do {
                                                        if ( $spr2[current($this->clientes)] != NULL)
                                                                array_push($spr,$spr2[current($this->clientes)]);
                                                        else
                                                                array_push($spr,0);
                                                } while ( next($this->clientes) );
                                                
                                                if ( $i2 < $i )
                                                        $i2 = $i;
                                                
                                                if ( $tmpdata[0] != $row["EndTime"] )   
                                                        array_push($whole_result,$spr);
                                                
                                                $spr = array();
                                                $spr2 = array();
                                        }

                                        for ( $i = 0; $i < count($whole_result); $i++ ) {  // To equal the arrays so that the graph is not unsquared. SP:Igualamos las matrices para que la gr�ica no se descuadre
                                                $tmp = count($whole_result[$i]);
                                                if ( $i2 < $tmp )                // Estupidez?. Check this code later...
                                                        continue;
                                                $tmp = $i2 - $tmp;
                                                for ( $a = 0; $a <= $tmp; $a++ )
                                                        array_push($whole_result[$i],"0");                                      // Fill the array
                                        }
                                        $resdata->free();       
//                                      echo "DEBUG:<br>";
//                                      echo "<pre>";
//                                      print_r ($whole_result);
//                                      echo "</pre>";  
                                        break;
                                
                                default:
                                        break;
                        }
                }
//      $result->free();
          return $whole_result;
        }//end function



        //Convert date from mysql to smarty.           THE SAME FUNCTION AT 2 CLASSES. THIS WAY IS BUGGY. TO SOLVE LATER.
        function PrepareDate($StartDateMonth,$StartDateDay,$StartDateYear,$EndDateMonth,$EndDateDay,$EndDateYear){
        
                $this->StartDate = $StartDateYear."-".$StartDateMonth."-".$StartDateDay." 00:00:00";
                $this->EndDate = $EndDateYear."-".$EndDateMonth."-".$EndDateDay." 23:59:00";
                
        }//end function


        function array_merge_php4($array1,$array2) {
            $return=array();

            foreach(func_get_args() as $arg) {
                if(!is_array($arg)){
                $arg=array($arg);
                }
                    foreach($arg as $key=>$val){
                            if(!is_int($key)){
                                $return[$key]=$val;
                            }else{
                                $return[]=$val;
                            }
                    }
            }
        return $return;
        }

}//end class

?>

