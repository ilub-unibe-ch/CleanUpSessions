<?php

require_once __DIR__ . "/../vendor/autoload.php";

use iLUB\Plugins\CleanUpSessions\UI\ConfigFormGUI;
use iLUB\Plugins\CleanUpSessions\Helper\CleanUpSessionsDBAccess;


/**
 * Class cleanUpSessionsConfigGUI
 */
class cleanUpSessionsConfigGUI extends cleanUpSessionsMainGUI
{
	const CMD_SAVE_CONFIG = 'saveConfig';
	const CMD_CANCEL = 'cancel';


	/**
	 * Creates a new ConfigFormGUI and sets the Content
	 */
	protected function index()
	{

		$form = new ConfigFormGUI($this, $this->DIC);
		$tpl = $this->DIC->ui()->mainTemplate();
		$tpl->setContent($form->getHTML());

	}


	/**
	 * Checks the form input and forwards to checkAndUpdate()
	 *
	 * @throws Exception
	 */
	protected function saveConfig()
	{
		$form = new ConfigFormGUI($this, $this->DIC);
		if ($form->checkInput()) {
			$this->checkAndUpdate($form->getInput(ilCleanUpSessionsPlugin::EXPIRATION_THRESHOLD));
		} else {
			ilUtil::sendFailure($this->pl->txt('msg_failed_save'), true);
		}
		$this->DIC->ctrl()->redirect($this);
	}

	/**
	 * $expiration_value must be numeric and bigger than 0 for the check to pass. If check passes value gets
	 * updated into DB
	 *
	 * @param int $expiration_value
	 * @throws Exception
	 */
	protected function checkAndUpdate(int $expiration_value)
	{
		$access = new CleanUpSessionsDBAccess($this->DIC);
		if (is_numeric($expiration_value) && (int)$expiration_value > 0) {
			$access->updateExpirationValue($expiration_value);
			ilUtil::sendSuccess($this->pl->txt('msg_successfully_saved'), true);
		} else {
			ilUtil::sendFailure($this->pl->txt('msg_not_valid_expiration_input'), true);
		}
	}

	/**
	 *
	 */
	protected function initTabs()
	{
		$this->DIC->tabs()->activateTab(self::TAB_PLUGIN_CONFIG);
	}
}
