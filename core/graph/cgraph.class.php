<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2016, Davide Franco			                            |
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
    private $data_type = array('pie' => 'text-data-single', 'bars' => 'text-data');
    private $uniform_data;
    private $data_colors = array( 'blue', 'orange', 'purple', 'red', 'green', 'SkyBlue', 'yellow', 'cyan', 'lavender', 'DimGrey');
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
    	}
    	
    	if( $data_ok === true && $label_ok === true)
    		return true;
    	else
    		return false;
    }

    // ==================================================================================
    // Function:    SetData()
    // Parameters:  $data_in
    //              $graph_type
    //              $uniform_data (set all values to same unit or not)
    // Return:      -
    // ==================================================================================

    public function SetData($data_in, $graph_type, $uniform_data = false)
    {
		$this->data = $data_in;
        $this->uniform_data = $uniform_data;

        // Check $this->data before creating the graph
        if (!$this->CheckData()) {
            throw new Exception('Passed $data_in variable is not an array');
        }

        if ($this->uniform_data) {
            $this->data = $this->UniformizeData($this->data);
        } else {
            $this->data = $data_in;
        }
        
        $this->plot->SetDataValues($this->data);
        
        $this->graph_type   = $graph_type;
        
        // Set graph type and data type
        $this->plot->SetPlotType($this->graph_type);
        $this->plot->SetDataType($this->data_type[$this->graph_type]);
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
        list($legend_width, $legend_height) = $this->plot->GetLegendSize();
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
    // Return:		true sum of values in the graph equal 0
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
        switch ($this->graph_type) {
            case 'pie':

                // Display message if data sum equal 0
                if ($this->isEmpty()) {
                    $message = "Sorry ...\nThere is not statistics to display\nfor the selected period :(";
                    $message_options = array( 'draw_background' => true, 'draw_border' => true, 'reset_font' => true, 'text_color' => 'black' );
                    $this->plot->DrawMessage($message, $message_options);
                } else {
                    // Set legend
                    $this->setLegend();
                    
                    // Set plot area
                    $this->plot->SetPlotAreaPixels($this->padding, $this->padding, ($this->width * 0.65), $this->height - $this->padding);

                    // Set graph colors and shading
                    $this->plot->SetDataColors($this->data_colors);
                    $this->plot->SetShading(0);
                    $this->plot->SetLabelScalePosition(0.5);
                }
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
