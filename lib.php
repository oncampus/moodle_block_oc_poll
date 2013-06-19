<?php

function delete_poll($id) {
	global $DB;
	$count_1 = $DB->count_records('block_oc_poll_answers', array('pollid' => $id));
	$count_2 = $DB->count_records('block_oc_poll_choices', array('pollid' => $id));
	if ($count_1 + $count_2 == 0) {
		$DB->delete_records('block_oc_poll_polls', array ('id' => $id));
		return true;
	}
	else {
		return false;
	}
}

function delete_answer($id, $pollid) {
	global $DB;
	$count = $DB->count_records('block_oc_poll_answers', array('pollid' => $pollid));
	if ($count == 0) {
		// erst die previous id dem nachfolger übergeben!!!!
		$this_choice = $DB->get_record('block_oc_poll_choices', array('id' => $id));
		$next_choice = $DB->get_record('block_oc_poll_choices', array('pollid' => $pollid, 'previous' => $id));
		if ($next_choice) {
			$next_choice->previous = $this_choice->previous;
			$DB->update_record('block_oc_poll_choices', $next_choice);
			$DB->delete_records('block_oc_poll_choices', array('id' => $id));
		}
		else {
			$DB->delete_records('block_oc_poll_choices', array('id' => $id));
		}
		return true;
	}
	else {
		return false;
	}
}

function get_current_polls() {
	global $DB;
	global $USER;
	
	$now = time();
	$results = $DB->get_records_sql('SELECT * FROM `mdl_block_oc_poll_polls` '.
									'WHERE (`start` = 0 or `start` <= ?) '.
									'and (`end` = 0 or `end` >= ?)', 
				array($now, $now));
	
	$polls = array();
	
	foreach ($results as $result) {
		$count_1 = $DB->count_records('block_oc_poll_answers', array('pollid' => $result->id, 'cid' => $USER->id));
		$count_2 = $DB->count_records('block_oc_poll_choices', array('pollid' => $result->id));
		if ($count_1 == 0 and $count_2 > 0) {
			$polls[] = $result;
		}
	}
	
	return $polls;
}

function get_poll_answers($pollid) {
	global $DB;	
	$result = get_choices_in_correct_order($pollid);
	if (!$result) {
		$result = $DB->get_records('block_oc_poll_choices', array('pollid' => $pollid));
	}
	return $result;
}

function set_poll_answer($pollid, $choiceid, $cid, $created) {
	global $DB;
	// wenn eintrag mit pollid, choiceid und cid schon existiert oder
	// wenn cid und pollid existiert und keine mehrfachauswahl erlaubt ist
	// dann kein insert
	$poll = $DB->get_record('block_oc_poll_polls', array('id' => $pollid));
	if ($poll->multipleanswers == 1) {
		$count = $DB->count_records('block_oc_poll_answers', array('pollid' => $pollid, 'choiceid' => $choiceid, 'cid' => $cid));
		if ($count > 0) {
			return;
		}
	}
	else {
		$count = $DB->count_records('block_oc_poll_answers', array('pollid' => $pollid, 'cid' => $cid));
		if ($count > 0) {
			return;
		}
	}
	$DB->insert_record('block_oc_poll_answers', array('pollid' => $pollid, 'choiceid' => $choiceid, 'cid' => $cid, 'created' => $created));
}

function get_poll($id) {
	global $DB;
	return $DB->get_record('block_oc_poll_polls', array('id' => $id));
}

function get_poll_results($pollid) {
	global $DB;
	$poll_results = new stdClass();
	$results = array();
	$result = get_choices_in_correct_order($pollid);
	if (!$result) {
		$result = $DB->get_records('block_oc_poll_choices', array('pollid' => $pollid));
	}
	foreach ($result as $choice) {
		if ($choice->headline == 1) {
			$count = -1;
		}
		else {
			$count = $DB->count_records('block_oc_poll_answers', array('pollid' => $pollid, 'choiceid' => $choice->id));
		}
		$results[$choice->text] = $count;
	}
	$participants = $DB->get_records_sql('SELECT COUNT(DISTINCT `cid`) as count '.
										 'FROM `mdl_block_oc_poll_answers` '.
										 'WHERE `pollid` = ?', 
										array($pollid));
	$count = 0;
	foreach ($participants as $participant) {
		$count = $participant->count;
	}
	//$participants = $participants[1]->count;
	$participants = $count;
	$poll_results->choices = $results;
	$poll_results->participants = $participants;
	return $poll_results;
}

function get_user_polls($userid) {
	global $DB;
	$count = 0;
	$polls = $DB->get_records_sql('SELECT COUNT(DISTINCT `pollid`) as polls '.
										 'FROM `mdl_block_oc_poll_answers` '.
										 'WHERE `cid` = ?',
										array($userid));
	foreach ($polls as $poll) {
		$count = $poll->polls;
	}
	return $count;
}

function get_last_answer($userid) {
	global $DB;
	$answer = $DB->get_records_sql('SELECT * '.
								  'FROM `mdl_block_oc_poll_answers` '.
								  'WHERE `cid` = ? '.
								  'ORDER BY `created` DESC '.
								  'LIMIT 0 , 1',
								array($userid));
	$result = '';
	foreach ($answer as $a) {
		$result = $a;
	}
	return $result;
}

// function get_last_choice($pollid) {
	// global $DB;
	// $choices = $DB->get_records_sql('SELECT * '.
									// 'FROM `mdl_block_oc_poll_choices` '.
									// 'WHERE `pollid` = ? '.
									// 'ORDER BY `id` DESC '.
									// 'LIMIT 1',
									// array($pollid));
	// $result = '';
	// foreach ($choices as $choice) {
		// $result = $choice;
	// }
	// return $result;
// }

function get_last_choice($pollid) {
	$choices = get_choices_in_correct_order($pollid);
	$result = '';
	if (!$choices) {
		return null;
	}
	foreach ($choices as $choice) {
		$result = $choice;
	}
	return $result;
}

function get_choices_in_correct_order($pollid) {
	global $DB;
	$choices = $DB->get_records('block_oc_poll_choices', array('pollid' => $pollid));
	$first_choice = $DB->get_record('block_oc_poll_choices', array('pollid' => $pollid, 'previous' => 0));
	if (!$first_choice) {
		return null;
	}
	$ordered_choices = array();
	$previous = $first_choice->id;
	$ordered_choices[] = $first_choice;
	$count = count($choices);
	$i = 0;
	while ($i < $count - 1) {
		foreach ($choices as $choice) {
			if (!in_array($choice, $ordered_choices) and $choice->previous == $previous) {
				$ordered_choices[] = $choice;
				$previous = $choice->id;
			}
		}
		$i++;
	}
	return $ordered_choices;
}

function move_choice($id, $up) {
	global $DB;
	$this_choice = $DB->get_record('block_oc_poll_choices', array('id' => $id));
	if ($up == true and $this_choice->previous != 0) {
		$previous_choice = $DB->get_record('block_oc_poll_choices', array('id' => $this_choice->previous, 'pollid' => $this_choice->pollid));
		$next_choice = $DB->get_record('block_oc_poll_choices', array('previous' => $this_choice->id, 'pollid' => $this_choice->pollid));
		if ($previous_choice) {
			$this_choice->previous = $previous_choice->previous;
			$previous_choice->previous = $this_choice->id;
			$DB->update_record('block_oc_poll_choices', $this_choice);
			$DB->update_record('block_oc_poll_choices', $previous_choice);
			if ($next_choice) {
				$next_choice->previous = $previous_choice->id;
				$DB->update_record('block_oc_poll_choices', $next_choice);
			}
		}
	}
	elseif ($up == false) {
		$next_choice = $DB->get_record('block_oc_poll_choices', array('previous' => $this_choice->id, 'pollid' => $this_choice->pollid));
		if ($next_choice) {
			move_choice($next_choice->id, true);
		}
	}
}
