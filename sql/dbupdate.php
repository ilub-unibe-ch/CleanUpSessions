<#1>
<?php
/** @var ilDB $ilDB */
global $ilDB;
$db = $ilDB;
require_once('Customizing/global/plugins/Services/Cron/CronHook/CleanUpSessions/classes/class.ilCleanUpSessionsPlugin.php');

if (!$db->tableExists(ilCleanUpSessionsPlugin::TABLE_NAME)) {
    $fields = array(
        'expiration' => array(
            'type'    => 'integer',
            'length'  => 4,
            'notnull' => true
        )
    );
    $db->createTable(ilCleanUpSessionsPlugin::TABLE_NAME, $fields);
    $db->insert(ilCleanUpSessionsPlugin::TABLE_NAME, array(
        ilCleanUpSessionsPlugin::COLUMN_NAME => array(
            'integer',
            ilCleanUpSessionsPlugin::DEFAULT_EXPIRATION_VALUE
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
        'timestamp'              => array(
            'type'    => 'integer',
            'length'  => '4',
            'notnull' => true
        ),
        'date'                   => array(
            'type'    => 'timestamp',
            'notnull' => true
        ),
        'deleted_anons'          => array(
            'type'   => 'integer',
            'length' => '4'
        ),
        'remaining_anons'        => array(
            'type'   => 'integer',
            'length' => '4'
        ),
        'all_remaining_sessions' => array(
            'type'   => 'integer',
            'length' => '4'
        )
    );
    $db->createTable(ilCleanUpSessionsPlugin::LOG_TABLE, $fields);
    $db->addPrimaryKey(ilCleanUpSessionsPlugin::LOG_TABLE, array('timestamp'));

}
?>

<#3>
<?php
/** @var ilDB $ilDB */
global $ilDB;
$db = $ilDB;
require_once('Customizing/global/plugins/Services/Cron/CronHook/CleanUpSessions/classes/class.ilCleanUpSessionsPlugin.php');
if (!$ilDB->tableColumnExists(ilCleanUpSessionsPlugin::LOG_TABLE, 'active_during_last_5min')) {
    $ilDB->addTableColumn(ilCleanUpSessionsPlugin::LOG_TABLE, 'active_during_last_5min', array(
        "type"   => "integer",
        "length" => '4'
    ));
}
if (!$ilDB->tableColumnExists(ilCleanUpSessionsPlugin::LOG_TABLE, 'active_during_last_15min')) {
    $ilDB->addTableColumn(ilCleanUpSessionsPlugin::LOG_TABLE, 'active_during_last_15min', array(
        "type"   => "integer",
        "length" => '4'
    ));

}
if (!$ilDB->tableColumnExists(ilCleanUpSessionsPlugin::LOG_TABLE, 'active_during_last_hour')) {
    $ilDB->addTableColumn(ilCleanUpSessionsPlugin::LOG_TABLE, 'active_during_last_hour', array(
        "type"   => "integer",
        "length" => '4'
    ));
}

?>
