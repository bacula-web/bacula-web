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

class Chart {

   public $name;
   protected $type;
   protected $data = array();
   protected $margin = 30;

   public function __construct($chart_data) {
      if( !is_array($chart_data)) {
         throw new Exception('Bad parameters provided to Chart constructor');
      }else {
         if( is_array($chart_data['data']) ) { $this->data = $chart_data['data']; }

         if( isset($chart_data['name']) ) { $this->name = $chart_data['name']; }
         if( isset($chart_data['type']) ) { $this->type = $chart_data['type']; }
      }
   }

   public function render() {
      $blob = '';
      $blob .= '<script type="text/javascript">' . "\n";

      // Transform PHP array to JSON 
      $json_data = array();

      foreach($this->data as $key => $val) {
         $json_data[] = array( 'label' => $val[0], 'value' => $val[1]);
      }

      // If the chart type is <bar>, prepare JSON data differently
      switch($this->type) {
      case 'pie':
         $blob .= $this->name . '_data = ' . json_encode( $json_data ) . ';' . "\n";
         break;
      case 'bar':
         $blob .= $this->name . '_data = ' . '[ {';
         $blob .= 'key: ' . '"Serie one"' . ',' . "\n";
         $blob .= 'values: ' . json_encode( $json_data) . '} ];';
      }
      $blob .= 'nv.addGraph(function() {';

      // Check chart type
      switch( $this->type ) {
      case 'pie':
         $blob .= 'var chart = nv.models.pieChart()' . "\n";
         break;
      case 'bar':
         $blob .= 'var chart = nv.models.discreteBarChart()' . "\n";
      }
      
      $blob .= '.x(function(d) {return d.label})' . "\n";
      $blob .= '.y(function(d) {return d.value})' . "\n";

      // If chart type is pie then show labels outside the slices
      if($this->type == 'pie') { 
         $blob .= '.showLabels(true)'."\n"; 
         $blob .= '.labelsOutside(true)'."\n";
         $blob .= '.growOnHover(true)'."\n";
      }
      // Set animation duration an staggerLabels for bar chart
      if($this->type == 'bar') { 
         $blob .= '.duration(500)' . "\n";
         $blob .= '.showValues(true)'."\n";
         $blob .= '.staggerLabels(true)'."\n";
      }

      // Set chart margins
      switch($this->type) {
      case 'bar':
         $blob .= '.margin({"top": ' . $this->margin . ',"right": ' . $this->margin . ',"left": 100,"bottom": ' . $this->margin . '})'."\n";
         break;
      default: 
         $blob .= '.margin({"top": ' . $this->margin . ',"right": ' . $this->margin . ',"left": ' . $this->margin . ',"bottom": ' . $this->margin . '})'."\n";
      }

      // Set colors
      switch($this->type) {
      case 'bar':
         $blob .= '.color(["#696969"]);';
         break;
      default:
         $blob .= '.color(["#4169E1","#FF8C00","#BA55D3","#FF0000","#7CFC00","#ADD8E6","#FFD700","#E0FFFF","#E6E6FA","#A9A9A9"]);';
      }

      $blob .= 'd3.select(\'#'.$this->name . ' svg\')' . "\n";

      $blob .= '.datum(' . $this->name . '_data )' . "\n" ;
      $blob .= '.call(chart);' . "\n";
              
      $blob .= 'nv.utils.windowResize(chart.update);';
      $blob .= 'return chart;';
      $blob .= ' });';
      
      $blob .= '</script>';

      return $blob;
   }
}    
