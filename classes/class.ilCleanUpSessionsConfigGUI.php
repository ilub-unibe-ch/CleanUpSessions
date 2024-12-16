<?php
declare(strict_types=1);

use iLUB\Plugins\CleanUpSessions\UI\ConfigFormGUI;
use iLUB\Plugins\CleanUpSessions\Helper\CleanUpSessionsDBAccess;
use ILIAS\DI\Container;

/**
 * Class ilCleanUpSessionsConfigGUI
 *  * @ilCtrl_IsCalledBy   ilCleanUpSessionsConfigGUI: ilObjComponentSettingsGUI
 */
class ilCleanUpSessionsConfigGUI extends ilPluginConfigGUI {
	const TAB_PLUGIN_CONFIG = 'tab_plugin_config';

	const CMD_INDEX = 'index';
	const CMD_SAVE_CONFIG = 'saveConfig';
	const CMD_CANCEL = 'cancel';

	protected ilCleanUpSessionsPlugin $pl;
	protected Container $DIC;
    protected ilGlobalTemplateInterface  $tpl;


	/**
	 * ilCleanUpSessionsConfigGUI constructor.
	 * @throws Exception
	 */
	public function __construct() {
		global $DIC;
		$this->DIC = $DIC;
		$this->pl = ilCleanUpSessionsPlugin::getInstance();
        $this->tpl = $DIC->ui()->mainTemplate();

	}

    /**
     * Creates a new ConfigFormGUI and sets the Content
     * @throws Exception
     */
	protected function index(): void{

		$form = new ConfigFormGUI($this, $this->DIC);
		$tpl = $this->DIC->ui()->mainTemplate();
		$tpl->setContent($form->getHTML());

	}

	/**
	 * Checks the form input and forwards to checkAndUpdate()
	 *
	 * @throws Exception
	 */
	protected function saveConfig(): void {
		$form = new ConfigFormGUI($this, $this->DIC);
		if ($form->checkInput()) {
			$this->checkAndUpdate((int)$form->getInput(ilCleanUpSessionsPlugin::EXPIRATION_THRESHOLD));
		} else {
            $this->tpl->setOnScreenMessage(IlGlobalTemplateInterface::MESSAGE_TYPE_FAILURE, $this->pl->txt('msg_failed_save'), true);
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
	protected function checkAndUpdate(int $expiration_value): void {
		$access = new CleanUpSessionsDBAccess($this->DIC);
		if ($expiration_value > 0) {
			$access->updateExpirationValue($expiration_value);
            $this->tpl->setOnScreenMessage(IlGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS, $this->pl->txt('msg_successfully_saved'), true);
		} else {
            $this->tpl->setOnScreenMessage(IlGlobalTemplateInterface::MESSAGE_TYPE_FAILURE, $this->pl->txt('msg_not_valid_expiration_input'), true);
		}
	}

	/**
	 *
	 */
	protected function initTabs(): void {
		$this->DIC->tabs()->activateTab(self::TAB_PLUGIN_CONFIG);
	}

    /**
     * @throws Exception
     */
    protected function cancel(): void {
		$this->index();
	}

    /**
     * @throws Exception
     * @throws Exception
     */
	public function performCommand(string $cmd): void {

			switch ($cmd) {
				case 'configure':
					$this->index();
					break;
				default:
					$this->$cmd();
			}


	}
}