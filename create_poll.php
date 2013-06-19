<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');
//require_once('lib.php');

require_login();

$context = get_context_instance(CONTEXT_SYSTEM);
if (!has_capability('block/oc_poll:managepolls', $context)) {
	redirect(new moodle_url('/'));
}

$blockname = get_string('create_poll', 'block_oc_poll');
$header = $blockname;

$PAGE->set_context($context);
$PAGE->navbar->add($blockname);
$PAGE->set_title($blockname . ': '. $header);
$PAGE->set_heading($blockname . ': '.$header);
$PAGE->set_url('/blocks/oc_poll/create_poll.php');
$PAGE->set_pagetype($blockname);

$output = $PAGE->get_renderer('block_oc_poll');
//echo $output->show_create_poll_form();

//include simplehtml_form.php
require_once('create_poll_form.php');
 
//Instantiate form 
$id = optional_param('pollid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_RAW);
	
if ($action == 'edit' and $id != 0) {
	$mform = new create_poll_form(new moodle_url('/blocks/oc_poll/create_poll.php?action=edit&pollid='.$id), array('pollid'=>$id));
}
else {
	$mform = new create_poll_form();
}

//$mform = new create_poll_form();
 
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
	redirect(new moodle_url('/blocks/oc_poll/manage_polls.php'));
} 
else if ($fromform = $mform->get_data()) {
	//In this case you process validated data. $mform->get_data() returns data posted in form.
	
	//print_object($fromform);
	
	$poll_record = new stdClass();
	$poll_record->titel = $fromform->title;
	$poll_record->question = $fromform->question['text'];
	// if ($fromform->description != '') {
		// $poll_record->description = $fromform->description;
	// }
	$poll_record->start = $fromform->start;
	$poll_record->end = $fromform->end;
	$poll_record->created = time();
	$poll_record->cid = $USER->id;
	$poll_record->active = 1;
	if (isset($fromform->multipleanswers)) {
		$poll_record->multipleanswers = 1;
	}
	else {
		$poll_record->multipleanswers = 0;
	}
	
	// echo $OUTPUT->header();
	// echo $OUTPUT->heading($blockname);
	//echo $mform->get_pollid();
	//print_object($fromform);
	//var_dump($_POST);
	if (isset($fromform->pollid)) {
		//echo 'moin';
		// TODO Update!
		$poll_record->id = $fromform->pollid;
		$DB->update_record('block_oc_poll_polls', $poll_record);
	}
	else {
		$DB->insert_record('block_oc_poll_polls', $poll_record);
	}
	
	//echo $output->confirm_msg_poll_created();
	redirect(new moodle_url('/blocks/oc_poll/manage_polls.php'));
} 
else {
	// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
	// or on the first display of the form.
	
	echo $OUTPUT->header();
	echo $OUTPUT->heading($blockname);

	//Set default data (if any)
	//$mform->set_data($toform);
	//displays the form
	$id = optional_param('pollid', 0, PARAM_INT);
    $action = optional_param('action', '', PARAM_RAW);
	
	if ($action == 'edit' and $id != 0) {
		$result = $DB->get_record('block_oc_poll_polls', array('id' => $id));
		//print_object($result);
		$toform = new stdClass();
		$toform->title = $result->titel;
		$toform->question['text'] = $result->question;
		//$toform->description = $result->description;
		$toform->start = $result->start;
		$toform->end = $result->end;
		$toform->multipleanswers = $result->multipleanswers;
		$toform->pollid = $id;
		
		$mform->set_data($toform);
	}
	$mform->display();
}

echo $OUTPUT->footer();