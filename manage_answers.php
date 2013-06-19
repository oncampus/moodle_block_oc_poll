<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');
require_once('lib.php');

require_login();

$context = get_context_instance(CONTEXT_SYSTEM);
if (!has_capability('block/oc_poll:managepolls', $context)) {
	redirect(new moodle_url('/'));
}

$pollid = optional_param('pollid', 0, PARAM_INT);
$id = optional_param('answerid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_RAW);
$confirm = optional_param('confirm', '', PARAM_RAW);

$blockname = get_string('manage_answers', 'block_oc_poll').' - '.get_poll($pollid)->titel;
$header = $blockname;

$PAGE->set_context($context);
$PAGE->navbar->add($blockname);
$PAGE->set_title($blockname . ': '. $header);
$PAGE->set_heading($blockname . ': '.$header);
$PAGE->set_url('/blocks/oc_poll/manage_answers.php');
$PAGE->set_pagetype($blockname);

if ($action == 'delete' and $id != 0) {
	if ($confirm != md5($id)) {
		echo $OUTPUT->header();
		echo $OUTPUT->heading($blockname);
		$title = optional_param('title', '', PARAM_RAW);
		$optionsyes = array('action' => $action, 'pollid' => $pollid, 'answerid' => $id, 'confirm'=>md5($id));
		echo $OUTPUT->confirm(get_string('deletecheckfull', '', "'$title'"), 
						new moodle_url('/blocks/oc_poll/manage_answers.php', $optionsyes), 
						new moodle_url('/blocks/oc_poll/manage_answers.php?pollid='.$pollid)
					);
		echo $OUTPUT->footer();
		die;
	}
	else {
		if (delete_answer($id, $pollid)) {
			redirect(new moodle_url('/blocks/oc_poll/manage_answers.php?pollid='.$pollid));
		}
		else {
			// could not delete answer
			echo $OUTPUT->header();
			echo $OUTPUT->heading($blockname);
			echo html_writer::tag('div', get_string('delete_answer_failed', 'block_oc_poll'));
			echo html_writer::tag('div', html_writer::link(new moodle_url('/blocks/oc_poll/manage_answers.php?pollid='.$pollid), get_string('back')));
			echo $OUTPUT->footer();
			die;
		}
	}
}
elseif ($action == 'moveup') {
	move_choice($id, true);
	redirect(new moodle_url('/blocks/oc_poll/manage_answers.php?pollid='.$pollid));
}
elseif ($action == 'movedown') {
	move_choice($id, false);
	redirect(new moodle_url('/blocks/oc_poll/manage_answers.php?pollid='.$pollid));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($blockname);

$output = $PAGE->get_renderer('block_oc_poll');
				
echo $output->link_create_answer($pollid);

echo $output->list_answers($pollid);

echo html_writer::tag('div', html_writer::link(new moodle_url('/blocks/oc_poll/manage_polls.php'), get_string('back')));

echo $OUTPUT->footer();