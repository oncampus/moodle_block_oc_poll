<?php

class block_oc_poll extends block_base {
    
	public function init() {
        $this->title = get_string('poll_block', 'block_oc_poll');
    }
	
	public function specialization() {
		// if (get_config('block_oc_poll', 'titel') != '') {
			// $this->title = get_config('block_oc_poll', 'titel');
		// }
	}

	public function instance_allow_multiple() {
		return true;
	}
	
	function has_config() {
		return true;
	}
	
	public function applicable_formats() {
		/* $systemcontext = get_context_instance(CONTEXT_SYSTEM);
		if (has_capability('block/oc_poll:myaddinstance', $systemcontext)) {
			return array('site' => true,
							'my' => true);
		}
		else {
			return array('all' => false);
		} */
		return array('my' => true);
	}
	
	public function get_content() {
		global $PAGE;
		global $CFG;
		global $USER;
		global $OUTPUT;
		require_once($CFG->dirroot.'/blocks/oc_poll/lib.php');
		
		if ($this->content !== null) {
			return $this->content;
		}
	 
		$this->content =  new stdClass;
		$systemcontext = get_context_instance(CONTEXT_SYSTEM);
		$output = $PAGE->get_renderer('block_oc_poll');
		$this->content->text = '';
		// if (!empty($this->config->text)) {
			// $this->content->text = html_writer::tag('div', $this->config->text).'<br />';
		// }				
		$this->content->text = html_writer::tag('div', get_config('block_oc_poll', 'titel')).'<br />';
		$polls = get_current_polls(); // nur polls anzeigen, die Antworten haben und noch nicht beantwortet wurden
		$this->content->text .= $output->display_polls($polls);
		$this->content->text .= '<br />';
		if (has_capability('block/oc_poll:managepolls', $systemcontext)) {
			$this->content->text .= '<div>'.html_writer::link(new moodle_url('/blocks/oc_poll/manage_polls.php'),
				get_string('manage_polls', 'block_oc_poll')).'</div>';
			
		}
		if (get_user_polls($USER->id) > 0) {
			$this->content->text .= $output->get_evaluate_link($USER->id);
		}
		//$this->content->text = html_writer::tag('p', $this->content->text);
		$this->content->footer = '';
		
		return $this->content;
	}
}