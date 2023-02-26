<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2023 Davide Franco
 *
 * This file is part of Bacula-Web.
 *
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see
 * <https://www.gnu.org/licenses/>.
 */

namespace Core\Graph;

use Core\Exception\AppException;
use Core\Utils\CUtils;
use TypeError;

class Chart
{
    public $name;
    protected $type;
    protected $data = array();
    protected $margin = 30;
    protected $ylabel = null;
    protected $uniformize_data = false;
    protected $linkedReport;

    /**
     * $chart_data is an array which the structure below
     *
     * data => array( key => value )
     * ylabel => label for Y axis (string)
     * type => 'bar' or 'pie' (string)
     * name => name of the chart (string)
     * uniformize_data => do we normalize data ? (boolean)
     * linked_report => linked report page
     *
     * @param [] $chart_data
     */
    public function __construct($chart_data)
    {
        if (!is_array($chart_data)) {
            throw new TypeError('Bad parameters provided to Chart constructor');
        } else {
            if (is_array($chart_data['data'])) {
                $this->data = $chart_data['data'];
            }

            if (isset($chart_data['name'])) {
                $this->name = $chart_data['name'];
            }
            if (isset($chart_data['type'])) {
                $this->type = $chart_data['type'];
            }
            if (isset($chart_data['ylabel'])) {
                $this->ylabel = $chart_data['ylabel'];
            }
            if (isset($chart_data['uniformize_data'])) {
                $this->uniformize_data = $chart_data['uniformize_data'];
            }
            if (isset($chart_data['linked_report'])) {
                $this->linkedReport = $chart_data['linked_report'];
            }
        }
    }

    /**
     * @return void
     */
    private function uniformizeData(): void
    {
        $array_sum = 0;

        // Uniformize data array element based on best unit
        foreach ($this->data as $key => $data) {
            if (is_null($data[1])) {
                $this->data[$key][1] = 0;
            }
        }

        // Calculate sum of all values
        foreach ($this->data as $value) {
            $array_sum += $value[1];
        }

        // Calculate average value and best unit
        $avg = $array_sum  / count($this->data);
        list($value, $best_unit) = explode(' ', CUtils::Get_Human_Size($avg, 1));

        foreach ($this->data as $key => $value) {
            $this->data[$key][1] = CUtils::Get_Human_Size($value[1], 1, $best_unit, false);
        }

        $this->ylabel = $best_unit;
    }

    /**
     * @return string
     * @throws AppException
     */
    public function render(): string
    {
        $blob = '<script type="text/javascript">' . "\n";

        // Uniformize data
        if ($this->uniformize_data === true) {
            $this->uniformizeData();
        }

        // Transform PHP array to JSON
        $json_data = array();

        foreach ($this->data as $key => $val) {
            $json_data[] = array( 'label' => $val[0], 'value' => intval($val[1]));
        }

        // If the chart type is <bar>, prepare JSON data differently
        switch ($this->type) {
            case 'pie':
                $blob .= $this->name . '_data = ' . json_encode($json_data) . ';' . "\n";
                break;
            case 'bar':
                $blob .= $this->name . '_data = ' . '[ {';
                $blob .= 'key: ' . '"Serie one"' . ',' . "\n";
                $blob .= 'values: ' . json_encode($json_data) . '} ];';
                break;
            default:
                throw new AppException('Unknown graph type');
        }
        $blob .= 'nv.addGraph(function() {' . "\n";

        // Check chart type
        switch ($this->type) {
            case 'pie':
                $blob .= 'var chart = nv.models.pieChart()' . "\n";
                break;
            case 'bar':
                $blob .= 'var chart = nv.models.discreteBarChart()' . "\n";
                break;
            default:
                throw new AppException('Unknown graph type');
        }

        $blob .= '.x(function(d) {return d.label})' . "\n";
        $blob .= '.y(function(d) {return d.value})' . "\n";

        // If chart type is pie then show labels outside the slices
        if ($this->type == 'pie') {
            $blob .= '.showLabels(true)' . "\n";
            $blob .= '.labelsOutside(true)' . "\n";
            $blob .= '.growOnHover(true)' . "\n";
            $blob .= '.labelType("percent")' . "\n";
            $blob .= '.valueFormat(d3.format(",.0d"))' . "\n";
        }
        // Set animation duration an staggerLabels for bar chart
        if ($this->type == 'bar') {
            $blob .= '.duration(500)' . "\n";
            $blob .= '.showValues(false)' . "\n";
            $blob .= '.staggerLabels(true)' . "\n";
            $blob .= '.showYAxis(true)' . "\n";
        }

        // Set chart margins
        switch ($this->type) {
            case 'bar':
                $blob .= '.margin({"top": ' . $this->margin . ',"right": ' . $this->margin . ',"left": 100,"bottom": ' . $this->margin . '})' . "\n";
                break;
            default:
                $blob .= '.margin({"top": ' . $this->margin . ',"right": ' . $this->margin . ',"left": ' . $this->margin . ',"bottom": ' . $this->margin . '})' . "\n";
        }

        // Set colors
        switch ($this->type) {
            case 'bar':
                $blob .= '.color(["#696969"]);';
                break;
            default:
                $blob .= '.color(["#696969","#32CD32","#FFD700", "#4169E1","#FF0000","#FF8C00","#ADD8E6","#FFD700","#E0FFFF","#E6E6FA","#A9A9A9"]);';
        }

        $blob .= "\n";

        if ($this->type == 'bar' && !is_null($this->ylabel)) {
                $blob .= "\n" . 'chart.yAxis' . "\n";
                $blob .= ".axisLabelDistance(25)\n";
                $blob .= ".axisLabel('" . $this->ylabel . "');\n";
        }

        $blob .= 'd3.select(\'#' . $this->name . ' svg\')' . "\n";

        $blob .= '.datum(' . $this->name . '_data )' . "\n" ;
        $blob .= '.call(chart);' . "\n";

        $blob .= 'nv.utils.windowResize(chart.update);';

        // Handle click event
        if ($this->type === 'pie' && !empty($this->linkedReport)) {
            $blob .= 'chart.pie.dispatch.on("elementClick", function(e) { window.location = "index.php?page=' . $this->linkedReport . '"; });';
        }

        $blob .= 'return chart;';
        $blob .= ' });';

        $blob .= '</script>';

        return $blob;
    }
}
