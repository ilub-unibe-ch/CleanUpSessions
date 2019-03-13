<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilCleanUpSessionsConfigGUI
 *
 * This GUI redirects commands to the cleanUpSessionsMainGUI
 *
 */
class ilCleanUpSessionsConfigGUI extends ilPluginConfigGUI {
	/**
	 * @inheritdoc
	 * @throws ilCtrlException
	 */
	public function executeCommand() {
		global $DIC;

		parent::executeCommand();
		switch ($DIC->ctrl()->getNextClass()) {
			case strtolower(cleanUpSessionsMainGUI::class):
				$mainGUI = new cleanUpSessionsMainGUI($DIC);
				$DIC->ctrl()->forwardCommand($mainGUI);
				return;
		}

		$DIC->ctrl()->redirectByClass([cleanUpSessionsMainGUI::class]);
	}

	/**
	 * @inheritdoc
	 * @param string $cmd
	 */
	public function performCommand($cmd) {
		// nothing to to here
	}
}