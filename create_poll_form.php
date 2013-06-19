<?php //moodleform is defined in formslib.php

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
 
class create_poll_form extends moodleform {
	//public $pollid;
    //Add elements to form
    function definition() {
        global $CFG;
 
        $mform = $this->_form; // Don't forget the underscore! 
		
		//$id = optional_param('pollid', 0, PARAM_INT);
		//$action = optional_param('action', '', PARAM_RAW);
		//if ($action == 'edit' and $id != 0) {
		
		if (isset($this->_customdata['pollid'])) {
			//echo 'moin';
			$mform->addElement('hidden', 'pollid', $this->_customdata['pollid']);
			$mform->setType('pollid', PARAM_INT);
			$mform->setConstant('pollid', $this->_customdata['pollid']);
		}
		
		//echo $this->_customdata['pollid'];
		//$this->pollid = 0;
		//$this->pollid = $this->_customdata['pollid'];
 
        $mform->addElement('text', 'title', get_string('poll_title', 'block_oc_poll'), array('size' => '78')); 	// Add elements to your form
		$mform->addRule('title', null, 'required', null, 'client');
        //$mform->setType('title', PARAM_NOTAGS);                   						//Set type of element
        //$mform->setDefault('title', get_string('poll_title', 'block_oc_poll'));        		//Default value
		
		// $mform->addElement('textarea', 'question', get_string('poll_question', 'block_oc_poll'), 'wrap="virtual" rows="10" cols="75"');
		// $mform->addRule('question', null, 'required', null, 'client');
		
		$mform->addElement('editor', 'question', get_string('poll_question', 'block_oc_poll'));
		$mform->setType('question', PARAM_RAW);
		$mform->addRule('question', null, 'required', null, 'client');
		
		//$mform->addElement('textarea', 'description', get_string('poll_description', 'block_oc_poll'), 'wrap="virtual" rows="5" cols="75"');
		$mform->addElement('date_time_selector', 'start', get_string('poll_start', 'block_oc_poll'), array('optional' => true));
		$mform->setDefault('start', time());
		$mform->addElement('date_time_selector', 'end', get_string('poll_end', 'block_oc_poll'), array('optional'=>true));
        $mform->setDefault('end', time()+7*24*3600);
		$mform->addElement('checkbox', 'multipleanswers', get_string('poll_multipleanswers', 'block_oc_poll'));
		
		$this->add_action_buttons(true, get_string('submit', 'block_oc_poll'));
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
	
	public function get_pollid() {
		return $this->pollid;
	}
}