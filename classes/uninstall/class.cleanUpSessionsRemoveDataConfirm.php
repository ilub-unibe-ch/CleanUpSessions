<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use iLUB\Plugins\CleanUpSessions\Helper\DIC;

/**
 * Class CleanUpSessionsRemoveDataConfirm
 *
 * @ilCtrl_isCalledBy CleanUpSessionsRemoveDataConfirm: ilUIPluginRouterGUI
 */
class CleanUpSessionsRemoveDataConfirm {

	use DIC;

	/**
	 * @param bool $plugin
	 */
	public static function saveParameterByClass(bool $plugin = true) {
		// TODO: ?
	}


	/**
	 * @var ilCleanUpSessionsPlugin
	 */
	protected $pl;


	/**
	 *
	 */
	public function __construct() {
		$this->pl = ilCleanUpSessionsPlugin::getInstance();
	}


	/**
	 *
	 */
	public function executeCommand() {
		// TODO: ?
	}


	/**
	 *
	 * @param string $html
	 */
	protected function show(string $html) {
		// TODO: ?
	}


	/**
	 * @param string $cmd
	 */
	protected function redirectToPlugins(string $cmd) {
		// TODO: ?
	}


	/**
	 *
	 */
	protected function cancel() {
		// TODO: ?
	}


	/**
	 *
	 */
	protected function confirmRemoveCleanUpSessionsData() {
		// TODO: ?
	}


	/**
	 *
	 */
	protected function deactivateCleanUpSessions() {
		// TODO: ?
	}


	/**
	 *
	 */
	protected function setKeepCleanUpSessionsData() {
		// TODO: ?
	}


	/**
	 *
	 */
	protected function setRemoveCleanUpSessionsData() {
		// TODO: ?
	}
}
