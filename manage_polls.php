<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');
require_once('lib.php');

require_login();

$context = get_context_instance(CONTEXT_SYSTEM);
if (!has_capability('block/oc_poll:managepolls', $context)) {
	redirect(new moodle_url('/'));
}

$blockname = get_string('manage_polls', 'block_oc_poll');
$header = $blockname;

$PAGE->set_context($context);
$PAGE->navbar->add($blockname);
$PAGE->set_title($blockname . ': '. $header);
$PAGE->set_heading($blockname . ': '.$header);
$PAGE->set_url('/blocks/oc_poll/manage_polls.php');
$PAGE->set_pagetype($blockname);

$id = optional_param('pollid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_RAW);
$confirm = optional_param('confirm', '', PARAM_RAW);

if ($action == 'delete' and $id != 0) {
	if ($confirm != md5($id)) {
		echo $OUTPUT->header();
		echo $OUTPUT->heading($blockname);
		$title = optional_param('title', '', PARAM_RAW);
		$optionsyes = array('action' => $action, 'pollid' => $id, 'confirm'=>md5($id));
		echo $OUTPUT->confirm(get_string('deletecheckfull', '', "'$title'"), 
						new moodle_url('/blocks/oc_poll/manage_polls.php', $optionsyes), 
						new moodle_url('/blocks/oc_poll/manage_polls.php')
					);
		echo $OUTPUT->footer();
		die;
	}
	else {
		if (delete_poll($id)) {
			redirect(new moodle_url('/blocks/oc_poll/manage_polls.php'));
		}
		else {
			// could not delete poll
			echo $OUTPUT->header();
			echo $OUTPUT->heading($blockname);
			echo html_writer::tag('div', get_string('delete_poll_failed', 'block_oc_poll'));
			echo html_writer::tag('div', html_writer::link(new moodle_url('/blocks/oc_poll/manage_polls.php'), get_string('back')));
			echo $OUTPUT->footer();
			die;
		}
	}
}

echo $OUTPUT->header();
echo $OUTPUT->heading($blockname);

$output = $PAGE->get_renderer('block_oc_poll');
				
echo $output->link_create_poll();

echo $output->list_polls();

echo html_writer::tag('div', html_writer::link(new moodle_url('/'), get_string('back')));

echo $OUTPUT->footer();