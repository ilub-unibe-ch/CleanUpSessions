<#1>
<?php
/** @var ilDB $ilDB */
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
<#2>
<?php
/** @var ilDB $ilDB */
global $ilDB;
$db = $ilDB;
require_once('Customizing/global/plugins/Services/Cron/CronHook/CleanUpSessions/classes/class.ilCleanUpSessionsPlugin.php');
if (!$db->tableExists(ilCleanUpSessionsPlugin::LOG_TABLE)) {
    $fields = array(
        'timestamp' => array(
            'type' => 'integer',
            'length' => '4',
            'notnull' => TRUE
        ),
        'date' => array(
            'type' => 'timestamp',
            'notnull' => TRUE
        ),
		'deleted_anons'=>array(
			'type'=>'integer',
			'length' => '4'
		),
		'remaining_anons'=>array(
			'type'=>'integer',
			'length' => '4'
		),
		'all_remaining_sessions'=>array(
			'type'=>'integer',
            'length' => '4'
		)
    );
    $db->createTable(ilCleanUpSessionsPlugin::LOG_TABLE, $fields);
    $db->addPrimaryKey(ilCleanUpSessionsPlugin::LOG_TABLE, array('timestamp'));


}
?>
