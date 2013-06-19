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
 * @package     local_home
 * @category    output
 * @copyright   2013 Your Name <your@email.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Defines rendering methods for the plugin.
 */
class block_oc_poll_renderer extends plugin_renderer_base {
	
    /**
     * One example of how renderer methods may look like - public methods
     *
     * @param stdClass $user
     * @return string
     */
	
	public function confirm_msg_poll_created() {
		return html_writer::tag('div', get_string('poll_created', 'block_oc_poll'));
	}
	
	public function link_create_poll() {
		$output = html_writer::tag('div', html_writer::link(new moodle_url('/blocks/oc_poll/create_poll.php'),
					get_string('create_poll', 'block_oc_poll'))).
					'<br />';
				
		return $output;
	}
	
	public function list_polls() {
		global $DB;
		
		$result = $DB->get_records('block_oc_poll_polls');
		//print_object($result);
		$table = new html_table();
		$table->head = array(
							get_string('poll_title', 'block_oc_poll'), 
							get_string('poll_question', 'block_oc_poll'),
							get_string('poll_start', 'block_oc_poll'),
							get_string('poll_end', 'block_oc_poll'),
							get_string('poll_multipleanswers', 'block_oc_poll'),
							get_string('actions', 'block_oc_poll')
						);
		$table_data = array();
		$count = 0;
		foreach ($result as $poll) {
			$links = html_writer::link(new moodle_url('/blocks/oc_poll/create_poll.php?action=edit&pollid='.$poll->id), get_string('edit', 'block_oc_poll')).' '.
					html_writer::link(new moodle_url('/blocks/oc_poll/manage_polls.php?action=delete&pollid='.$poll->id.'&title='.$poll->titel), get_string('delete', 'block_oc_poll')).' '.
					html_writer::link(new moodle_url('/blocks/oc_poll/manage_answers.php?pollid='.$poll->id), get_string('answers', 'block_oc_poll')).' '.
					html_writer::link(new moodle_url('/blocks/oc_poll/evaluate_poll.php?pollid='.$poll->id), get_string('evaluate', 'block_oc_poll'));
			$start_date = '-';
			if ($poll->start > 0) {
				$start_date = date('d.m.Y H:i', $poll->start);
			}
			$end_date = '-';
			if ($poll->end > 0) {
				$end_date = date('d.m.Y H:i', $poll->end);
			}
			
			$multiple = get_string('yes');
			if ($poll->multipleanswers == 0) {
				$multiple = get_string('no');
			}
			$table_data[$count] = array(
									$poll->titel,
									$poll->question,
									$start_date,
									$end_date,
									$multiple,
									$links
								);
			$count++;
		}
		
		$table->data = $table_data;
		return html_writer::table($table);
	}
	
	public function link_create_answer($pollid) {
		$output = html_writer::tag('div', html_writer::link(new moodle_url('/blocks/oc_poll/edit_answer.php?pollid='.$pollid),
					get_string('create_answer', 'block_oc_poll'))).
					'<br />';
				
		return $output;
	}
	
	public function list_answers($pollid) {
		global $DB, $OUTPUT;
		$ordered = true;
		$result = get_choices_in_correct_order($pollid);
		if (!$result) {
			$result = $DB->get_records('block_oc_poll_choices', array('pollid' => $pollid));
			$ordered = false;
		}

		//print_object($result);
		$table = new html_table();
		$table->head = array(
							get_string('answer_text', 'block_oc_poll'),
							get_string('headline', 'block_oc_poll'),
							get_string('actions', 'block_oc_poll')
						);
		$table_data = array();
		$count = 0;
		$count_answers = count($result);
		foreach ($result as $answer) {
			$links = '';
			if ($ordered == true) {
				if ($count != 0) {
					$links .= html_writer::link(new moodle_url('/blocks/oc_poll/manage_answers.php?action=moveup&pollid='.$answer->pollid.'&answerid='.$answer->id), '<img src="'.$OUTPUT->pix_url('t/up').'" class="iconsmall" alt="'.get_string('moveup').'" />').' ';
				}
				if ($count != $count_answers - 1) {
					$links .= html_writer::link(new moodle_url('/blocks/oc_poll/manage_answers.php?action=movedown&pollid='.$answer->pollid.'&answerid='.$answer->id), '<img src="'.$OUTPUT->pix_url('t/down').'" class="iconsmall" alt="'.get_string('movedown').'" />').' ';
				}
			}
			$links .= html_writer::link(new moodle_url('/blocks/oc_poll/edit_answer.php?action=edit&pollid='.$answer->pollid.'&answerid='.$answer->id), get_string('edit', 'block_oc_poll')).' '.
					html_writer::link(new moodle_url('/blocks/oc_poll/manage_answers.php?action=delete&pollid='.$answer->pollid.'&title='.$answer->text.'&answerid='.$answer->id), get_string('delete', 'block_oc_poll'));
			
			$answer_text = $answer->text;
			$headline = get_string('no');
			if ($answer->headline == 1) {
				$answer_text = html_writer::tag('h4', $answer_text);
				$headline = get_string('yes');
			}
			
			$table_data[$count] = array(
									$answer_text,
									$headline,
									$links
								);
			$count++;
		}
		
		$table->data = $table_data;
		return html_writer::table($table);
	}
	
	public function display_polls($polls) {
		global $CFG, $USER;
		require_once($CFG->dirroot.'/blocks/oc_poll/lib.php');
		//require_once("$CFG->libdir/formslib.php");
		$output = '';
		foreach ($polls as $poll) {
			$output .= html_writer::tag('div', 
							html_writer::tag('h3', $poll->titel).
							html_writer::tag('p', $poll->question)
						);
			$answers = get_poll_answers($poll->id);
			//print_object($answers);
			$form = '';
			foreach ($answers as $answer) {
				if ($poll->multipleanswers == 1) {
					if ($answer->headline == 1) {
						$form .= html_writer::tag('p', $answer->text.':', array('style' => 'font-style: italic;'));
					}
					else {
						$form .= html_writer::tag('div', html_writer::tag('input', '', array('type' => 'checkbox', 'name' => 'choice_'.$answer->id, 'value' => $answer->id)).' '.
									html_writer::tag('label', $answer->text)
								);
					}
				}
				else {
					if ($answer->headline == 1) {
						$form .= html_writer::tag('p', $answer->text.':', array('style' => 'font-style: italic;'));
					}
					else {
						if ($form == '') {
							$form .= html_writer::tag('div', html_writer::tag('input', '', array('type' => 'radio', 'name' => 'choice', 'checked' => 'checked', 'value' => $answer->id)).' '.
									html_writer::tag('label', $answer->text)
								);
						}
						else {
							$form .= html_writer::tag('div', html_writer::tag('input', '', array('type' => 'radio', 'name' => 'choice', 'value' => $answer->id)).' '.
									html_writer::tag('label', $answer->text)
								);
						}
					}
				}
				//$output .= html_writer::tag('div', html_writer::link(new moodle_url('/'), $answer->text));
			}
			if ($poll->multipleanswers == 1) {
				$form .= html_writer::tag('div', html_writer::tag('input', '', array('type' => 'hidden', 'name' => 'multiple', 'value' => 1)));
			}
			else {
				$form .= html_writer::tag('div', html_writer::tag('input', '', array('type' => 'hidden', 'name' => 'multiple', 'value' => 0)));
			}
			$form .= html_writer::tag('div', html_writer::tag('input', '', array('type' => 'hidden', 'name' => 'pollid', 'value' => $poll->id)));
			$form .= html_writer::tag('div', html_writer::tag('input', '', array('type' => 'hidden', 'name' => 'cid', 'value' => $USER->id)));
			$form .= '<br />';
			$form .= html_writer::tag('div', html_writer::tag('input', '', array('type' => 'submit', 'value' => get_string('answer', 'block_oc_poll'))));
			$form = html_writer::tag('form', $form, array('method' => 'post', 'action' => new moodle_url('/blocks/oc_poll/evaluate.php')));
			//$output .= html_writer::tag('form', $form);
			$output .= $form;
		}
		if ($output == '') {
			$output = html_writer::tag('div', get_string('no_polls', 'block_oc_poll'));
		}
		return $output;
	}
	
	public function get_poll_graph($poll, $poll_results) {
		$max_bar_width = 200;
		$out = '';
		$total_answers = 0;
		$participants = $poll_results->participants;
		$keys = array_keys($poll_results->choices);
		foreach ($keys as $key) {
			if ($poll_results->choices[$key] >= 0) {
				$total_answers += $poll_results->choices[$key];
			}
		}
		foreach ($keys as $key) {
			$answers = $poll_results->choices[$key];
			if ($answers >= 0) { // if answers == -1 -> headline
				if ($participants > 0) {
					if ($poll->multipleanswers == 0) {
						$width = $answers / $total_answers * $max_bar_width;
					}
					else {
						$width = $answers / $participants * $max_bar_width;
					}
					$p = number_format($answers *  100 / $participants, 1);
				}
				else {
					$width = 0;
					$p = '0.0';
				}
				$out .= $key.' ('.$answers.')<br />';
				$out .= html_writer::tag('div', 
										 html_writer::tag('div', 
														$p.'%', 
														array('style' => 'width: '.$width.'px; height: 15px; border: 0px; background: #ccc; text-align: center;')), 
										 array('style' => 'width: '.$max_bar_width.'px; height: 15px; border: 1px solid #999;')).'<br />';
			}
			else {
				$out .= html_writer::tag('p', $key.':', array('style' => 'font-style: italic;'));
			}
		}
		
		$return = html_writer::tag('h2', $poll->titel).
			html_writer::tag('h4', $poll->question).
			html_writer::tag('div', get_string('participants').': '.$participants).
			html_writer::tag('div', get_string('total_answers', 'block_oc_poll').': '.$total_answers).
			html_writer::tag('div', '<br />').
			html_writer::tag('div', $out);
		return $return;
	}
	
	public function get_evaluate_link($userid) {
		return html_writer::tag('div', html_writer::link(new moodle_url('/blocks/oc_poll/evaluate_poll.php?userid='.$userid), get_string('evaluate', 'block_oc_poll')));
	}
}
