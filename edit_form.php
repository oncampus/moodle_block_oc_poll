<?php
 
class block_oc_poll_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
 
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
 
        // A sample string variable with a default value.
        $mform->addElement('textarea', 'config_text', get_string('blockstring', 'block_oc_poll'));
        $mform->setDefault('config_text', '');
        $mform->setType('config_text', PARAM_MULTILANG);        
 
    }
}