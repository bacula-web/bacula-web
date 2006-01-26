<?
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



        function Bweb() {
                
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
                $this->dsn['phptype'] = $conf->get(CONFIG_FILE,$sec,"db_type");   // mysql, pgsql
                if (  $conf->get(CONFIG_FILE,$sec,"db_port") )
                        $this->dsn[port] = $conf->get(CONFIG_FILE,$sec,"db_port");
                        
                $this->link =& $this->connect($this->dsn);
                
                if (DB::isError($this->link))
                    die($this->link->getMessage());
                $this->driver = $this->dsn[phptype];                            
                register_shutdown_function(array(&$this,'close'));
		$this->dbs_name = $this->dsn['database'];
	}
                
        function close() {

                $this->link->disconnect();
        }      

        
         
        function CalculateBytesPeriod($server,$StartDate,$EndPeriod) {   // Bytes transferred in a period.

                $result =& $this->link->query("select SUM(JobBytes) from Job WHERE EndTime < '$EndPeriod' and EndTime > '$StartDate' and Name='$server'")
                        or die("classes.inc: Error query: 1");
                $return =& $result->fetchRow(); 
                return $return[0];
        }//end function

        
         
        function CalculateFilesPeriod($server,$StartDate,$EndPeriod) {    // Number of files transferred in a period.

                $result =& $this->link->query("select SUM(JobFiles) from Job WHERE EndTime < '$EndPeriod' and EndTime > '$StartDate' and Name='$server'")
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
                $res = $this->link->query("SELECT Name FROM Pool");
                while ( $tmp =& $res->fetchRow() ) {
                        if ($this->driver == "mysql" )
                                $result = $this->link->query("select Media.VolumeName, Media.VolBytes,Media.VolStatus,Pool.Name,Media.MediaType,Media.LastWritten,FROM_UNIXTIME(UNIX_TIMESTAMP(Media.LastWritten)+Media.VolRetention ) as expire from Pool LEFT JOIN Media ON Media.PoolId=Pool.PoolId where Name='$tmp[0]' order by Media.VolumeName");
                        else if ($this->driver == "pgsql")
				$result = $this->link->query("select Media.VolumeName, Media.VolBytes,Media.VolStatus,Pool.Name,Media.MediaType,Media.LastWritten, Media.LastWritten + Media.VolRetention * interval '1 second' as expire from Pool LEFT JOIN Media ON Media.PoolId=Pool.PoolId where Name='$tmp[0]' order by Media.VolumeName");
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
        
        function GetDbSize() {
                if ( $this->driver == "mysql") {
                        $dbsize = $this->link->query("show table status")
                                or die ("classes.inc: Error query: 3");
                        if ( $dbsize->numRows() ) {
                                while ( $res = $dbsize->fetchRow(DB_FETCHMODE_ASSOC) )
                                        $database_size += $res["Data_length"];
                        }
                        else
                                return 0;
                }
                else if ( $this->driver == "pgsql") {
                        $dbsize = $this->link->query("select pg_database_size('$this->dbs_name')")
                                or die ("classes.inc: Error query: 4");
			if (PEAR::isError($dbsize))
	                    die($dbsize->getMessage());
                        if ( $dbsize->numRows() ) {
                                while ( $res = $dbsize->fetchRow() )
                                        $database_size += $res[0];
                        }
                        else
                                return 0;
                }       
        $dbsize->free();
        return $database_size;  
        }

}

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
                        ImageString($img, 5, 4, 4, "None data to process", $txc);
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
                        $result = $DB_bacula->link->query("select $derecha,$izquierda from Job where Name='$servidor' and EndTime < '$this->EndDate' and EndTime > '$this->StartDate' order by SchedTime asc")
                                or die ("classes.inc: Error at query: 5");
                while ( $row = $result->fetchRow(DB_FETCHMODE_ASSOC) ) {
                        $whole_result[] = $this->array_merge_php4($row["$derecha"],$row[$izquierda]);
                }
                $result->free();
        } else {                                                // Complex graph. 3 or more data.
                
                        switch ( $tipo_dato )
                                {
                                case '30':                      // Unused, at this time.
                                        $result = $DB_bacula->link->query("select JobBytes,JobFiles,Jobid from Job where Name='$servidor' order by EndTime asc")
                                                or die ("classes.inc: Error at query: 6");
                                        while ( $row = $result->fetchRow(DB_FETCHMODE_ASSOC) )
                                                $whole_result[] = array_merge($row["Jobid"],$row["JobFiles"],$row["JobBytes"]);
                                        $result->free();
                                        break;
                                case BACULA_TYPE_BYTES_ENDTIME_ALLJOBS:  // Special: Generic graph from all clientes.
                                        $i = -1;                         // Counter of number of jobs of one client. SP: Contador del nmero de jobs totales de un cliente.
                                        $i2 = 0;                         // Counter of number of keys of array. SP: Contador del nmero de valores del array.
                                        
                                        if ($DB_bacula->driver == "mysql") {
                                        $res = $DB_bacula->link->query("select Name from Job where UNIX_TIMESTAMP(EndTime) > UNIX_TIMESTAMP(NOW())-$this->elapsed  group by Name order by Name desc")
                                                or die ("classes.inc: Error at query: 7");
                                                $resdata = $DB_bacula->link->query("select date_format(EndTime,\"%Y-%m-%d\") from Job where UNIX_TIMESTAMP(EndTime) > UNIX_TIMESTAMP(NOW())-$this->elapsed  group by date_format(EndTime, \"%Y-%m-%d\") order by EndTime")
                                                        or die ("classes.inc: Error at query: 8");
					}
                                        else if ($DB_bacula->driver == "pgsql") {
						$res = $DB_bacula->link->query("select Name from Job where EndTime > now() - 1*interval'$this->elapsed s'  group by Name order by Name desc")
							or die ("classes.inc: Error at query: 8");
                                                $resdata = $DB_bacula->link->query("select to_char(EndTime,'YY-MM-DD') from Job where EndTime > NOW() - 1*interval'$this->elapsed s'  group by EndTime order by EndTime")
                                                        or die ("classes.inc: Error at query: 9");
					}
                                        
					if (PEAR::isError($resdata))
						die("classes.inc: Error at query: 9.1<br>".$resdata->getMessage());
                                        while ( $tmpdata = $res->fetchRow() )
                                                array_push($this->clientes,$tmpdata[0]);
                                                
//                                      echo "<pre>";
//                                      print_r ($this->clientes);
//                                      echo "</pre>";
                                        
                                        
                                        $spr = array();                        // Temporal array
                                        $spr2 = array();                       // Temporal array
                                        $whole_result = array();
                                        
                                        while ( $tmpdata = $resdata->fetchRow() ) {
                                                $count++;
                                                array_push($spr,$tmpdata[0]);
                                                if ($DB_bacula->driver == "mysql")
                                                        $result = $DB_bacula->link->query("select date_format(EndTime,\"%Y-%m-%d\"),SUM(JobBytes) as sum,Name as name,count(Name) as Nname from Job WHERE EndTime like '$tmpdata[0]%' group by Name order by Name desc")
                                                                or die ("classes.inc: Error at query: 10");
                                                else if ($DB_bacula->driver == "pgsql") {
							$query = "select to_char(EndTime,'YY-MM-DD'),SUM(JobBytes) as sum,Name,count(Name) as Nname from Job WHERE EndTime like '%$tmpdata[0]%' group by EndTime,Name order by Name desc";
                                                        $result = $DB_bacula->link->query($query)
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
