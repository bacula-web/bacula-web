<?
/* 
+-------------------------------------------------------------------------+
| Copyright (C) 2004 Juan Luis Francés Jiménez                            |
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

// Create a graph, showing a img. 	SP: Genera el gráfico.
// $server= Client of backup. 		SP: Servidor de Backup
// $tipo_dato= Type of data			SP: Datos a mostar
// $title= Title of graph.			SP: Titulo de la gráfica
// $xlabel= Leyend X axis.			SP: Leyenda eje X
// $ylabel= Leyend Y axis.			SP: Leyenda eje Y
// $sizex
// $sizey
// $MBottom = Margin of the bottom of the graph
// $modo_graph= Type of graph (bars, lines, linepoints, area, points, and pie).
// $elapsed = Period in seconds to show complex graph (tipo_dato <3) 1 month = 18144000
session_start();
require ("classes.inc");

$graph = new BCreateGraph();


if ( isset($_GET['sizey']) && isset($_GET['sizex']) ) {
	$graph->sizey = $_GET['sizey'];
	$graph->sizex = $_GET['sizex'];
}

if ( isset($_GET['MBottom']) ) {
	$graph->MarginBottom = $_GET['MBottom'];
}

if ( isset($_GET['EndDateYear']) )
	$graph->PrepareDate($_GET['StartDateMonth'],$_GET['StartDateDay'],$_GET['StartDateYear'],$_GET['EndDateMonth'],$_GET['EndDateDay'],$_GET['EndDateYear']);

if ( isset($_GET['legend']) )
	$graph->Leg = $_GET['legend'];
	
if ( isset($_GET['elapsed']) )
	$graph->elapsed = $_GET['elapsed'];
	
if (!isset($_GET['modo_graph']) )
	$graph->BCreate ($_GET['server'],$_GET['tipo_dato'],$_GET['title']);
else if (!isset($_GET['xlabel']))
	$graph->BCreate ($_GET['server'],$_GET['tipo_dato'],$_GET['title'],$_GET['modo_graph']);
else if (!isset($_GET['ylabel']))
	$graph->BCreate ($_GET['server'],$_GET['tipo_dato'],$_GET['title'],$_GET['modo_graph'],$_GET['xlabel']);
else
	$graph->BCreate ($_GET['server'],$_GET['tipo_dato'],$_GET['title'],$_GET['modo_graph'],$_GET['xlabel'],$_GET['ylabel']);


?>
