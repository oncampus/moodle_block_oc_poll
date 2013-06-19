<?php
 
function xmldb_block_oc_poll_upgrade($oldversion) {
    global $CFG, $DB;
	
	$dbman = $DB->get_manager();
 
    $result = TRUE;
 
	if ($oldversion < 2013041101) {

        // Define field headline to be added to block_oc_poll_choices
        $table = new xmldb_table('block_oc_poll_choices');
        $field = new xmldb_field('headline', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'previous');

        // Conditionally launch add field headline
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // oc_poll savepoint reached
        upgrade_block_savepoint(true, 2013041101, 'oc_poll');
    }
 
    return $result;
}
?>