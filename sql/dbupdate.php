<#1>
<?php
global $ilDB;
$db = $ilDB;
require_once('Customizing/global/plugins/Services/Cron/CronHook/CleanUpSessions/classes/class.ilCleanUpSessionsPlugin.php');

if (!$db->tableExists(ilCleanUpSessionsPlugin::TABLE_NAME)) {
	$fields = array(
		'expiration' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => TRUE
		)
	);
	$db->createTable(ilCleanUpSessionsPlugin::TABLE_NAME, $fields);
	$db->insert(ilCleanUpSessionsPlugin::TABLE_NAME, array(
		ilCleanUpSessionsPlugin::COLUMN_NAME => array(
			'integer', ilCleanUpSessionsPlugin::DEFAULT_EXPIRATION_VALUE
		)
	));
}
?>