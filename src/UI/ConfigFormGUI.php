<?php
declare(strict_types=1);
namespace iLUB\Plugins\CleanUpSessions\UI;

use ilCleanUpSessionsConfigGUI;
use ilCleanUpSessionsPlugin;
use ilPropertyFormGUI;
use ilTextInputGUI;
use iLUB\Plugins\CleanUpSessions\Helper\CleanUpSessionsDBAccess;
use ILIAS\DI\Container;

/**
 * Class ConfigFOrmGUI
 * initializes GUI
 *
 * @package iLUB\Plugins\CleanUpSessions\UI
 */
class ConfigFormGUI extends ilPropertyFormGUI {

	protected ilCleanUpSessionsConfigGUI $parent_gui;
	protected ilCleanUpSessionsConfigGUI $config;
	protected ilCleanUpSessionsPlugin $pl;
	protected cleanUpSessionsDBAccess $access;
	protected Container $DIC;


	/**
	 * @throws \Exception
	 */
	public function __construct(ilCleanUpSessionsConfigGUI $parent_gui, Container $dic) {
		$this->DIC = $dic;
		$this->parent_gui = $parent_gui;
		$this->access = new cleanUpSessionsDBAccess($this->DIC);
		$this->pl = ilCleanUpSessionsPlugin::getInstance();
		$this->setFormAction($this->DIC->ctrl()->getFormAction($this->parent_gui));
		$this->initForm();
		$this->addCommandButton(ilCleanUpSessionsConfigGUI::CMD_SAVE_CONFIG, $this->pl->txt('button_save'));
		$this->addCommandButton(ilCleanUpSessionsConfigGUI::CMD_CANCEL, $this->pl->txt('button_cancel'));
		parent::__construct();
	}


	protected function initForm(): void  {
		$this->setTitle($this->pl->txt('admin_form_title'));

		$item = new ilTextInputGUI($this->pl->txt('expiration_threshold'), ilCleanUpSessionsPlugin::EXPIRATION_THRESHOLD);
		$item->setInfo($this->pl->txt('expiration_info'));
		$item->setValue($this->access->getExpirationValue());
		$this->addItem($item);
	}
}
