<?php

require_once __DIR__ . "/../vendor/autoload.php";

use iLUB\Plugins\CleanUpSessions\UI\ConfigFormGUI;
use iLUB\Plugins\CleanUpSessions\Helper\CleanUpSessionsDBAccess;

/**
 * Class ilCleanUpSessionsConfigGUI
 *  * @ilCtrl_IsCalledBy   ilObjComponentSettingsGUI
 */
class ilCleanUpSessionsConfigGUI extends ilPluginConfigGUI
{
    const TAB_PLUGIN_CONFIG = 'tab_plugin_config';

    const CMD_INDEX = 'index';
    const CMD_SAVE_CONFIG = 'saveConfig';
    const CMD_CANCEL = 'cancel';

    /**
     * @var ilCleanUpSessionsPlugin
     */
    protected $pl;

    /**
     * @var $DIC
     */
    protected $DIC;

    /**
     * ilCleanUpSessionsConfigGUI constructor.
     * @throws Exception
     */
    public function __construct()
    {
        global $DIC;
        $this->DIC = $DIC;
        $this->pl  = ilCleanUpSessionsPlugin::getInstance();

    }

    /**
     * Creates a new ConfigFormGUI and sets the Content
     */
    protected function index()
    {

        $form = new ConfigFormGUI($this, $this->DIC);
        $tpl  = $this->DIC->ui()->mainTemplate();
        $tpl->setContent($form->getHTML());

    }

    /**
     * Checks the form input and forwards to checkAndUpdate()
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
     * @param int $expiration_value
     * @throws Exception
     */
    protected function checkAndUpdate($expiration_value)
    {
        $access = new CleanUpSessionsDBAccess($this->DIC);
        if (is_numeric($expiration_value) && (int) $expiration_value > 0) {
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

    /**
     *
     */
    protected function cancel()
    {
        $this->index();
    }

    /**
     * @inheritdoc
     * @param string $cmd
     */
    public function performCommand($cmd)
    {

        switch ($cmd) {
            case 'configure':
                $this->index();
                break;
            default:
                $this->$cmd();
        }

    }
}