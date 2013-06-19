<?php

require_once('../../config.php');
require_once('lib.php');

require_login();

$data = $_POST;
//print_object($data);

$pollid = $data['pollid'];
$cid = $data['cid'];
$multiple = $data['multiple'];

// only one answer possible
if ($multiple == 0) {
	$choiceid = $data['choice'];
	set_poll_answer($pollid, $choiceid, $cid, time());
	redirect(new moodle_url('/'));
}
else {
	//choice_
	$keys = array_keys($data);
	foreach ($keys as $key) {
		if (strpos($key, 'choice_') === 0)  {
			//echo $key.': '.$data[$key].'<br />';
			set_poll_answer($pollid, $data[$key], $cid, time());
		}
	}
	redirect(new moodle_url('/'));
}