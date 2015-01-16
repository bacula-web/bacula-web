<?php

 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2015, Davide Franco			                            |
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

class CGraph {
	
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

    function __construct($filename, $width = 400, $height = 260) {
        // Set image file relative path
        $this->img_filename = str_replace( BW_ROOT . '/', '', VIEW_CACHE_DIR) . '/' . $filename;

        // Set image widht and height
        $this->width        = $width;
        $this->height       = $height;

        $this->plot = new PHPlot($this->width, $this->height, $this->img_filename);    
    }

    public function SetData($data_in, $graph_type, $uniform_data = false) {
        $this->uniform_data = $uniform_data;

	if( $this->uniform_data )
            $this->data = $this->UniformizeData($data_in);
	else
            $this->data	= $data_in;
		
        $this->graph_type   = $graph_type;
    }
 
    // ==================================================================================
    // Function: 	UniformizeData()
    // Parameters:	$data_in (array of values to uniformize)
    // Return:	    array of uniformized values
    // ==================================================================================
	
    public function UniformizeData($data_in) {
	$array_sum = 0;
	$best_unit = '';

    // Uniformize data array element based on best unit
	foreach( $data_in as $key => $data ) {
	    if( is_null($data[1]) ) 
	        $data_in[$key][1] = 0;
	}

	// Calculate sum of all values
        foreach( $data_in as $value)
	    $array_sum += $value[1];

	// Calculate average value and best unit
 	$avg = $array_sum  / count($data_in);
 	list($value,$best_unit) = explode(' ', CUtils::Get_Human_Size($avg, 1) );

        foreach($data_in as $key => $value) {
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
	
    private function get_Filepath() {
		return $this->img_filename;
    }

    // ==================================================================================
    // Function: 	SetYTitle()
    // Parameters:	$ytitle (Y axis title)
    // Return:	    -
    // ==================================================================================
	
    public function SetYTitle($ytitle) {
      $this->plot->SetYTitle($ytitle);
    }
	
    // ==================================================================================
    // Function: 	setLegend()
    // Parameters:	none
    // Return:	    -
    // ==================================================================================
	
	private function setLegend() {
		// Setting graph legend values
		$legends = array();
    
		foreach ($this->data as $key => $legend) {
			$this->plot->SetLegend(implode(': ', $legend));
		}

		// Legend position (calculated regarding the width and height of the graph)
		list($legend_width, $legend_height) = $this->plot->GetLegendSize();
		$this->plot->SetLegendPixels($this->width - ($legend_width + 5), 10);	
	}
	
    // ==================================================================================
    // Function:    setPieLegendColors()
    // Parameters:  array variable containing list of color codes (eg: #eeefff) 
	// Return:	    -	
	// ==================================================================================
	
	public function setPieLegendColors( $colors = array() ) {
		$this->data_colors = $colors;
	}

    // ==================================================================================
	// Function: 	Render()
	// Parameters:	none
	// Return:		graph image file path
	// ==================================================================================
	
	public function Render() {

        // Render to file instead of screen
        $this->plot->SetFileFormat("jpg");
        $this->plot->SetIsInline(true);

        // Set graph type and data type
        $this->plot->SetPlotType( $this->graph_type );
        $this->plot->SetDataType( $this->data_type[$this->graph_type] );
		      
		// Set graph values
		$this->plot->SetDataValues($this->data);
		
		// Check if provided datas for the graph are a valid and non empty array (to be improved)
		if( is_null($this->data) or empty($this->data) ) {
			$message_options = array( 'draw_background' => TRUE, 'draw_border' => TRUE, 'reset_font' => TRUE, 'text_color' => 'black' );
			$this->plot->DrawMessage('No statistics to display', $message_options);
		}else {
            // Set Font
            $this->plot->SetFont( 'x_label', '2');
            $this->plot->SetFont( 'y_label', '2');
            
			// Set image border type
			$this->plot->SetImageBorderType('none');

			switch ( $this->graph_type ) {
				case 'pie':
					// Set legend
					$this->setLegend();
					
					// Set plot area
					$this->plot->SetPlotAreaPixels($this->padding, $this->padding, ($this->width * 0.65), $this->height - $this->padding);

					// Set graph colors and shading
					$this->plot->SetDataColors( $this->data_colors );
					$this->plot->SetShading( 0 );
					$this->plot->SetLabelScalePosition(0.5);
					
					break;
				case 'bars':
					// X label angle
					$this->plot->SetXLabelAngle(90);

					// Plot and border colors
					$this->plot->SetDataColors(array('gray'));
					$this->plot->SetDataBorderColors(array('black'));

					// Shading
					$this->plot->SetShading( 2 );
					break;
			}

			# Turn off X tick labels and ticks because they don't apply here:
			$this->plot->SetXTickLabelPos('none');
			$this->plot->SetXTickPos('none');
			$this->plot->SetPlotAreaWorld(NULL, 0, NULL, NULL);
			
			// Graph rendering
			$this->plot->DrawGraph();
		}

		// Return image file path
		return $this->get_Filepath();
		
    } // end function Render()
	
}  // end CGraph class
?>
