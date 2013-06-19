<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');
require_once('lib.php');

require_login();

$context = get_context_instance(CONTEXT_SYSTEM);

$blockname = get_string('evaluate', 'block_oc_poll');
$header = $blockname;

$PAGE->set_context($context);
$PAGE->navbar->add($blockname);
$PAGE->set_title($blockname . ': '. $header);
$PAGE->set_heading($blockname . ': '.$header);
$PAGE->set_url('/blocks/oc_poll/evaluate_poll.php');
$PAGE->set_pagetype($blockname);

$id = optional_param('pollid', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

$output = $PAGE->get_renderer('block_oc_poll');

$graph = 'graph';
$out = '';
if (has_capability('block/oc_poll:managepolls', $context) and $userid == 0) {
	if ($id == 0) {			// evaluate all polls
		
	}
	else if ($id > 0) {		// evaluate one single poll
		// $imageurl = new moodle_url('/blocks/oc_poll/poll_graph.php', array('pollid' => $id));
		// $graphname = get_string('evaluate', 'block_oc_poll');
		// $out = html_writer::tag('div', html_writer::empty_tag('img',
				// array('src' => $imageurl, 'alt' => $graphname)),
				// array('class' => 'graph'));
		$poll_results = get_poll_results($id);
		$poll = get_poll($id);
		$out = $output->get_poll_graph($poll, $poll_results);
		$out .= html_writer::tag('div', '<br />'.html_writer::link(new moodle_url('/blocks/oc_poll/manage_polls.php'), get_string('back')));
	}
}
else if ($userid > 0) {		// evaluate the last poll, the user took part in
	$answer = get_last_answer($userid);
	$poll_results = get_poll_results($answer->pollid);
	$poll = get_poll($answer->pollid);
	$out = $output->get_poll_graph($poll, $poll_results);
	$out .= html_writer::tag('div', '<br />'.html_writer::link(new moodle_url('/'), get_string('back')));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($blockname);

echo $out;

echo $OUTPUT->footer();