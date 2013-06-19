<?php

$capabilities = array(
 
    'block/oc_poll:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
			'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
			'manager' => CAP_ALLOW,
			'user' => CAP_ALLOW
        )
    ),
 
    'block/oc_poll:addinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
			'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
			'manager' => CAP_ALLOW,
			'user' => CAP_ALLOW
        )
    ),
	
	'block/oc_poll:managepolls' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
			'manager' => CAP_ALLOW
        )
    )

);