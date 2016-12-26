<?php
	
class Highcharts
{
	private $id, $type, $title, $series, $drilldown, $showLegend, $colorByPoint, $formatBytes, $enable3D;
	
	public function __construct($id, $type, $title, $series, $drilldown, $options = array())
	{
		$this->id           = $id;
		$this->type         = $type;
		$this->title        = $title;
		$this->series       = $series;
		$this->drilldown    = $drilldown;
		
		$this->showLegend   = (isset($options['showLegend'])) ? $options['showLegend'] : 0;
		$this->colorByPoint = (isset($options['colorByPoint'])) ? $options['colorByPoint'] : 1;
		$this->formatBytes = (isset($options['formatBytes'])) ? $options['formatBytes'] : 0;
		$this->formatScale = (isset($options['formatScale'])) ? $options['formatScale'] : 0;
		$this->enable3D = (isset($options['enable3D'])) ? $options['enable3D'] : 0;
	}
	
	public function get_graph_js()
	{
		$js = "Highcharts.chart('".$this->id."', {
		        chart: {";
		        if($this->enable3D)
		        {
		        $js .= "options3d: {
		                enabled: true,
		                alpha: 45,
		                beta: 0
		            },";
		        }   
		$js .=      "type: '".$this->type."'
		        },
		        title: {
		            text: ''
		        },
		        xAxis: {
		            type: 'category',
		        },
		        yAxis: {
		            labels: {";
		            if($this->formatBytes)
					{
			            $js .= "formatter: function() { return bytes(this.value, true);}";
			        }
		$js .=     "},
		            title: {
			            text: '".$this->title."'
			        }
			    },
			    legend: {
		            enabled: ".(int)$this->showLegend."
		        },
		        plotOptions: {
		            series: {
		                dataLabels: {
		                    enabled: true,";
		                    if($this->formatBytes || $this->formatScale)
		                    {
			                    $js .= "formatter: function() { return bytes(this.y, true, ".$this->formatScale.");}";
		                    }
		                    else
		                    {
			                    $js .= "format: '{point.name}: {point.y:.0f}'";
		                    }
		$js .=             "}
		            },
		            ".$this->type.": {";
		            if($this->showLegend) $js .= "showInLegend: true,";
		$js .=         	"depth: 35
		            }
		        },
		        tooltip: {
		            headerFormat: '<span style=\"font-size:11px\">{series.name}</span><br>',";
                    if($this->formatBytes || $this->formatScale)
                    {
	                    $js .= "pointFormatter: function() { return '<b>' + this.name + ':</b> ' + bytes(this.y, true, ".$this->formatScale.");}";
                    }
                    else
                    {
	                    $js .= "pointFormat: '<span style=\"color:{point.color}\">{point.name}</span>: <b>{point.y:.0f}</b><br/>'";
                    }
		$js .=     "
		        },
		        series: [{
			        name: '".$this->title."',
		            colorByPoint: ".(int)$this->colorByPoint.",
		            cursor: 'pointer',
		            point: {
		                events: {
		                    click: function() {
		                        if(this.options && 'url' in this.options) location.href = this.options.url;
		                    }
		                }
		            },
		            data: [";
		$max = count($this->series);
		$x = 1;
		foreach($this->series as $serie)
		{    
		$js .=      "{
		                name: '".$serie[0]."',";
		                
		                if(isset($serie[2]['color'])) $js .= "color: '".$serie[2]['color']."',";
		                if(isset($serie[2]['url'])) $js .= "url: '".$serie[2]['url']."',";
		                if(isset($serie[2]['drilldown'])) $js .= "drilldown: '".$serie[2]['drilldown']."',";
		$js .=         "y: ".$serie[1]."
		            }";
		if($x < $max) $js .= ',';
		$x++;
		}
		
		$js .=      "]}]";
		
		if($this->drilldown)
		{
			$js .= ",drilldown: {
						series: [";
			$max = count($this->drilldown);
			$x = 1;		
			foreach($this->drilldown as $drilldown)
			{
				$js .= "{\n\tname: '".$drilldown['name']."',\n";
				$js .= "id: '".$drilldown['id']."',\n";
				$js .= "data: [\n";
				$max2 = count($drilldown['data']);
				$x2 = 1;
				foreach($drilldown['data'] as $d)
				{
					$js .= "['".$d[0]."',".$d[1]."]";
					if($x2 < $max2) $js .= ",\n";
					$x2++;
				}
				$js .= "]\n}";
				
				if($x < $max) $js .= ",\n";
				$x++;
			}		
						
			$js .= "]}\n";
		}
		
		$js .=   "});";
		
		return $js;
	}
}