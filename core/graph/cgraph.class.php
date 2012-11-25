<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2012, Davide Franco			                          |
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
    private $ytitle;
	
    private $data;
    private $data_type = array('pie' => 'text-data-single', 'bars' => 'text-data');
    private $graph_type;
	
    private $width;
    private $height;
	
    public $img_filename;
    private $plot;

    function __construct($filename = "graph.png") {
        $this->img_filename = VIEW_CACHE_DIR . '/' . $filename;
    }

    public function SetData($data_in, $graph_type) {
        $this->data 	   = $data_in;
        $this->graph_type  = $graph_type;
    }

    public function SetGraphSize($width, $height) {
        $this->width = $width;
        $this->height = $height;
    }

    public function SetYTitle($ytitle) {
        if (!empty($ytitle))
            $this->ytitle = $ytitle;
        else
            die("Please provide a non empty title for the Y axis");
    }

    private function get_Filepath() {
		return $this->img_filename;
    }

    public function Render() {
        // Setting the size
        $this->plot = new PHPlot($this->width, $this->height);

        // Render to file instead of screen
        $this->plot->SetOutputFile($this->img_filename);
        $this->plot->SetFileFormat("png");
        $this->plot->SetIsInline(true);

        // Set graph type and data type
        $this->plot->SetPlotType( $this->graph_type );
        $this->plot->SetDataType( $this->data_type[$this->graph_type] );
		      
		// Set graph values
		$this->plot->SetDataValues($this->data);

        // Set image border type
        $this->plot->SetImageBorderType('none');

        switch ( $this->graph_type ) {
            case 'pie':
                $this->plot->SetPlotAreaPixels(5, 5, ($this->width / 2), $this->height - 5);
                $this->plot->SetLabelScalePosition(0.2);

                // Set legend data
                $legends = array();
                foreach ($this->data as $key => $legend)
                    $this->plot->SetLegend(implode(': ', $legend));

                // Legend position (calculated regarding the width and height of the graph)
                list($legend_width, $legend_height) = $this->plot->GetLegendSize();
                $this->plot->SetLegendPixels($this->width - ($legend_width + 5), 10);

                // Set graph colors and shading
				$data_colors = array( 'blue', 'green', 'orange', 'red', 'black', 'yellow', 'cyan', 'lavender', 'DimGrey');
				$this->plot->SetDataColors( $data_colors );
				$this->plot->SetShading( 0 );
				
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

        // Set graph Y axis title
        $this->plot->SetYTitle($this->ytitle);

        # Turn off X tick labels and ticks because they don't apply here:
        $this->plot->SetXTickLabelPos('none');
        $this->plot->SetXTickPos('none');
        $this->plot->SetPlotAreaWorld(NULL, 0, NULL, NULL);

        // Graph rendering
        $this->plot->DrawGraph();
		
		// Return image file path
		return $this->get_Filepath();
		
    } // end function Render()
	
}  // end CGraph class
?>
