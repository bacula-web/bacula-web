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

class CGraph
{
    
    private $data;
    private $data_ok;
    private $data_type = array( 'pie' => 'text-data-single', 
    							'bars' => 'text-data');
    private $uniform_data;
    private $data_colors = array( 'blue', 'orange', 'purple', 'red', 'green', 
    							  'SkyBlue', 'yellow', 'cyan', 'lavender', 'DimGrey');
    private $graph_type;
    
    private $width;
    private $height;
    private $padding = 5;
    
    public $img_filename;
    private $plot;

    // ==================================================================================
    // Function:    _construct()
    // Parameters:  $filename (graph output filename)
    //              $width (graph width)
    //              $height (graph height)
    // Return:      -
    // ==================================================================================

    public function __construct($filename, $width = 400, $height = 260)
    {
        // Set image file relative path
        $this->img_filename = str_replace(BW_ROOT . '/', '', VIEW_CACHE_DIR) . '/' . $filename;

        // Set image widht and height
        $this->width        = $width;
        $this->height       = $height;

        $this->plot = new PHPlot($this->width, $this->height, $this->img_filename);

        // Set Font
        $this->plot->SetFont('x_label', '2');
        $this->plot->SetFont('y_label', '2');
        
        // Set image border type
        $this->plot->SetImageBorderType('none');

        // Render to file instead of screen
        $this->plot->SetFileFormat("jpg");
        $this->plot->SetIsInline(true);
    }
    
    // ==================================================================================
    // Function:    CheckData()
    // Return:      true or false
    // Description: check if data class property class->data is an array, contain at least one sub-array and
    // if the array does contain at least a non null value.
    // ==================================================================================

    private function CheckData() {
    	$label_ok = true;
    	$data_ok = false;
    	
    	// Check if $values is an array
    	if( !is_array($this->data) ) {
    		return false;
    	}
    	
    	// Check if the array contain at least one element
    	if( count($this->data) == 0 )
    		return false;

    	// Check if $values does not contain null or empty values
    	foreach($this->data as $row) {
    		// check if first element of the sub-array is a valid string
    		if( !is_string($row[0]) ) { 
    			$label_ok = false;
    		}
    		// check if at least one second element of the sub-array is set (not null and contain value)
    		if( isset($row[1]) ) { 
    			$data_ok = true;
    		}
    	} // end foreach
    	
    	if( $data_ok === true && $label_ok === true)
    		return true;
    	else
    		return false;
    }

    // ==================================================================================
    // Function:    SetData()
    // Parameters:  $data_in
    //              $graph_type
    //              $uniform_data (set all values to the same unit or not)
    // Return:      -
    // ==================================================================================

    public function SetData($data_in, $graph_type, $uniform_data = false)
    {
        
		$this->data = $data_in;
		$this->uniform_data = $uniform_data;
		
		// Check $this->data before creating the graph
        $this->data_ok = $this->CheckData();
        
        // Set array values to same unit but only if values are clean
        if ($this->uniform_data === true AND $this->data_ok === true) {
            $this->data = $this->UniformizeData($this->data);
        }
        
        // Set plot values and other stuffs only if provided values are clean
		if($this->data_ok === true) {
		        // Set graph datas, type and data type
        		$this->graph_type   = $graph_type;
        		$this->plot->SetPlotType($this->graph_type);
        		$this->plot->SetDataValues($this->data); 
        		$this->plot->SetDataType($this->data_type[$this->graph_type]);
        }
    }
 
    // ==================================================================================
    // Function: 	UniformizeData()
    // Parameters:	$data_in (array of values to uniformize)
    // Return:	    array of uniformized values
    // ==================================================================================

    public function UniformizeData($data_in)
    {
        $array_sum = 0;
        $best_unit = '';

        // Uniformize data array element based on best unit
        foreach ($data_in as $key => $data) {
            if (is_null($data[1])) {
                $data_in[$key][1] = 0;
            }
        }

        // Calculate sum of all values
        foreach ($data_in as $value) {
            $array_sum += $value[1];
        }

        // Calculate average value and best unit
        $avg = $array_sum  / count($data_in);
        list($value, $best_unit) = explode(' ', CUtils::Get_Human_Size($avg, 1));

        foreach ($data_in as $key => $value) {
            $data_in[$key][1] = CUtils::Get_Human_Size($value[1], 1, $best_unit, false);
        }

        $this->plot->SetYTitle($best_unit);

        return $data_in;
    }

    // ==================================================================================
    // Function: 	get_Filepath()
    // Parameters:	none
    // Return:	    Graph file path
    // ==================================================================================

    private function get_Filepath()
    {
        return $this->img_filename;
    }

    // ==================================================================================
    // Function: 	SetYTitle()
    // Parameters:	$ytitle (Y axis title)
    // Return:	    -
    // ==================================================================================

    public function SetYTitle($ytitle)
    {
        $this->plot->SetYTitle($ytitle);
    }
    
    // ==================================================================================
    // Function: 	setLegend()
    // Parameters:	none
    // Return:	    -
    // ==================================================================================

    private function setLegend()
    {
        // Setting graph legend values
        foreach ($this->data as $key => $legend) {
            $this->plot->SetLegend(implode(': ', $legend));
        }

        // Set Legend position (calculated regarding the width and height of the graph)
        $legendsize = $this->plot->GetLegendSize();
        $legend_width = $legendsize[0];
        $this->plot->SetLegendPixels($this->width - ($legend_width + 5), 10);
    }
    
    // ==================================================================================
    // Function:    setPieLegendColors()
    // Parameters:  array variable containing list of color codes (eg: #eeefff)
    // Return:	    -
    // ==================================================================================

    public function setPieLegendColors($colors = array())
    {
        $this->data_colors = $colors;
    }

    // ==================================================================================
    // Function: 	isEmpty()
    // Parameters:	none
    // Return:		true (boolean) if sum of values in the graph is equal to 0
    // ==================================================================================

    protected function isEmpty()
    {
        $array_sum = 0;

        foreach ($this->data as $val) {
            $array_sum += $val[1];
        }
        
        if ($array_sum == 0) {
            return true;
        } else {
            return false;
        }
    }
    
    // ==================================================================================
    // Function: 	Render()
    // Parameters:	none
    // Return:		graph image file path
    // ==================================================================================

    public function Render()
    {
        $nostats_msg = "Sorry ...\nThere is not statistics to display\nfor the selected period :(";
        $msg_options = array( 	'draw_background' => true, 
        							'draw_border' => true, 
        							'reset_font' => true, 
        							'text_color' => 'black' );

    	// Check if passed values to graph are ok first
    	if( $this->data_ok !== true ) {
    		$this->plot->DrawMessage( $nostats_msg, $msg_options);
        }else {
        	// Everything is fine, we can draw the graph :)
			switch ($this->graph_type) {
				case 'pie':
					// Set legend
					$this->setLegend();
				
					// Set plot area
					$this->plot->SetPlotAreaPixels($this->padding, $this->padding, ($this->width * 0.65), $this->height - $this->padding);

					// Set graph colors and shading
					$this->plot->SetDataColors($this->data_colors);
					$this->plot->SetShading(0);
					$this->plot->SetLabelScalePosition(0.5);
				break;
				case 'bars':
					// X label angle
					$this->plot->SetXLabelAngle(90);

					// Plot and border colors
					$this->plot->SetDataColors(array('gray'));
					$this->plot->SetDataBorderColors(array('black'));

					// Shading
					$this->plot->SetShading(2);
				break;
			} // end switch
		} // end else
		
        # Turn off X tick labels and ticks because they don't apply here:
        $this->plot->SetXTickLabelPos('none');
        $this->plot->SetXTickPos('none');
        $this->plot->SetPlotAreaWorld(null, 0, null, null);
        
        // Graph rendering
        $this->plot->DrawGraph();

         // Return image file path
        return $this->get_Filepath();
    } // end function Render()
    
}  // end CGraph class
