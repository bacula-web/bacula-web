<?php
require_once 'external_packages/phplot/phplot.php';

$plot = new PHPlot(400, 300);

$data = array(
  array('', 100, 100, 200, 100),
  array('', 150, 100, 150, 100),
);
$plot->SetImageBorderType('plain');
$plot->SetDataType('text-data');
$plot->SetDataValues($data);
$plot->SetPlotType('pie');
//$plot->SetShading( '5' )

$plot->DrawGraph();

?>