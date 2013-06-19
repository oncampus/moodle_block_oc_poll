<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file renders the quiz overview graph.
 *
 * @package   oc_poll
 * @copyright 2012 Jan Rieger
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot.'/blocks/oc_poll/lib.php');
require_once($CFG->libdir . '/graphlib.php');

$pollid = required_param('id', PARAM_INT);
echo 'moin';
$line = new graph(800, 600);
$line->parameter['title'] = 'Evaluation';
$line->parameter['y_label_left'] = 'y-achse'; //get_string('participants');
$line->parameter['x_label'] = 'x-achse'; //get_string('grade');
$line->parameter['y_label_angle'] = 90;
$line->parameter['x_label_angle'] = 0;
$line->parameter['x_axis_angle'] = 60;
//$line->parameter['bar_size'] = 1;
//$line->parameter['bar_spacing'] = 10;
$line->parameter['y_min_left'] = 0;
$line->parameter['y_max_left'] = 5;
$line->parameter['y_decimal_left'] = 0;
$line->parameter['y_axis_gridlines'] = 2;

// The following two lines seem to silence notice warnings from graphlib.php.
//$line->y_tick_labels = null;
//$line->offset_relation = null;


$line->x_data = array('1', '2', '3');

$line->y_format['a'] = array(
    'colour' => 'red',
    'bar' => 'fill',
    //'shadow_offset' => 1,
    'legend' => 'Legende',
	'bar_size' => 1
);
$line->y_data['a'] = array('1', '4', '3');
$line->y_data = array('1', '2', '3');

//$line->y_order = array('allusers');

$line->draw();
