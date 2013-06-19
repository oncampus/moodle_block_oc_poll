<?php //moodleform is defined in formslib.php

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
 
class edit_answer_form extends moodleform {
    //Add elements to form
    function definition() {
        global $CFG;
 
        $mform = $this->_form; // Don't forget the underscore! 
		
		if (isset($this->_customdata['answerid'])) {
			$mform->addElement('hidden', 'answerid', $this->_customdata['answerid']);
			$mform->setType('answerid', PARAM_INT);
			$mform->setConstant('answerid', $this->_customdata['answerid']);
		}
		$mform->addElement('hidden', 'pollid', $this->_customdata['pollid']);
		$mform->setType('pollid', PARAM_INT);
		$mform->setConstant('pollid', $this->_customdata['pollid']);
		$mform->addElement('textarea', 'text', get_string('answer_text', 'block_oc_poll'), 'wrap="virtual" rows="10" cols="75"');
		$mform->addRule('text', null, 'required', null, 'client');
		$mform->addElement('checkbox', 'headline', get_string('headline', 'block_oc_poll'));
		
		$this->add_action_buttons(true, get_string('submit', 'block_oc_poll'));
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}