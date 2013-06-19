<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');
require_once('lib.php');

require_login();

$context = get_context_instance(CONTEXT_SYSTEM);
if (!has_capability('block/oc_poll:managepolls', $context)) {
	redirect(new moodle_url('/'));
}

$blockname = get_string('create_answer', 'block_oc_poll');
$header = $blockname;

$PAGE->set_context($context);
$PAGE->navbar->add($blockname);
$PAGE->set_title($blockname . ': '. $header);
$PAGE->set_heading($blockname . ': '.$header);
$PAGE->set_url('/blocks/oc_poll/edit_answer.php');
$PAGE->set_pagetype($blockname);

$output = $PAGE->get_renderer('block_oc_poll');
//echo $output->show_create_poll_form();

//include simplehtml_form.php
require_once('edit_answer_form.php');
 
//Instantiate form 
$id = optional_param('answerid', 0, PARAM_INT);
$pollid = optional_param('pollid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_RAW);
	
if ($action == 'edit' and $id != 0) {
	$mform = new edit_answer_form(new moodle_url('/blocks/oc_poll/edit_answer.php?action=edit&answerid='.$id), array('answerid'=>$id, 'pollid' => $pollid));
}
else {
	$mform = new edit_answer_form(new moodle_url('/blocks/oc_poll/edit_answer.php'), array('pollid' => $pollid));
}
 
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
	redirect(new moodle_url('/blocks/oc_poll/manage_answers.php?pollid='.$pollid));
} 
else if ($fromform = $mform->get_data()) {
	//In this case you process validated data. $mform->get_data() returns data posted in form.
	
	$choice_record = new stdClass();
	$choice_record->text = $fromform->text;
	$choice_record->pollid = $fromform->pollid;
	if (isset($fromform->headline)) {
		$choice_record->headline = $fromform->headline;
	}
	else {
		$choice_record->headline = 0;
	}
	
	if (isset($fromform->answerid)) {
		$choice_record->id = $fromform->answerid;
		$DB->update_record('block_oc_poll_choices', $choice_record);
	}
	else {
		$last_choice = get_last_choice($fromform->pollid);
		if ($last_choice != '') {
			$choice_record->previous = $last_choice->id;
		}
		else {
			$choice_record->previous = 0;
		}
		$DB->insert_record('block_oc_poll_choices', $choice_record);
	}
	redirect(new moodle_url('/blocks/oc_poll/manage_answers.php?pollid='.$pollid));
}
else {
	// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
	// or on the first display of the form.
	
	echo $OUTPUT->header();
	echo $OUTPUT->heading($blockname);

	//Set default data (if any)
	//displays the form
	$id = optional_param('answerid', 0, PARAM_INT);
    $action = optional_param('action', '', PARAM_RAW);
	
	if ($action == 'edit' and $id != 0) {
		$result = $DB->get_record('block_oc_poll_choices', array('id' => $id));
		$toform = new stdClass();
		$toform->text = $result->text;
		$toform->headline = $result->headline;
		
		$mform->set_data($toform);
	}
	$mform->display();
}

echo $OUTPUT->footer();