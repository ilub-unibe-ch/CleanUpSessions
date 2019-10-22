<?php

namespace iLUB\Plugins\CleanUpSessions\UI;

use ilCleanUpSessionsConfigGUI;
use ilCleanUpSessionsPlugin;
use ilPropertyFormGUI;
use ilTextInputGUI;
use iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess;

/**
 * Class ConfigFOrmGUI
 * initializes GUI
 * @package iLUB\Plugins\CleanUpSessions\UI
 */
class ConfigFormGUI extends ilPropertyFormGUI
{

    /**
     * @var ilCleanUpSessionsConfigGUI
     */
    protected $parent_gui;
    /**
     * @var ICleanUpSessions
     */
    protected $config;
    /**
     * @var ilCleanUpSessionsPlugin
     */
    protected $pl;
    /**
     * @var cleanUpSessionsDBAccess
     */
    protected $access;
    /**
     * @var dic
     */
    protected $DIC;

    /**
     * ConfigFormGUI constructor.
     * @param $parent_gui
     * @param $dic
     * @throws \Exception
     */
    public function __construct($parent_gui, $dic)
    {
        $this->DIC        = $dic;
        $this->parent_gui = $parent_gui;
        $this->access     = new cleanUpSessionsDBAccess($this->DIC);
        $this->pl         = ilCleanUpSessionsPlugin::getInstance();
        $this->setFormAction($this->DIC->ctrl()->getFormAction($this->parent_gui));
        $this->initForm();
        $this->addCommandButton(ilCleanUpSessionsConfigGUI::CMD_SAVE_CONFIG, $this->pl->txt('button_save'));
        $this->addCommandButton(ilCleanUpSessionsConfigGUI::CMD_CANCEL, $this->pl->txt('button_cancel'));
        parent::__construct();
    }

    /**
     *
     */
    protected function initForm()
    {
        $this->setTitle($this->pl->txt('admin_form_title'));

        $item = new ilTextInputGUI($this->pl->txt('expiration_threshold'), ilCleanUpSessionsPlugin::EXPIRATION_THRESHOLD);
        $item->setInfo($this->pl->txt('expiration_info'));
        $item->setValue($this->access->getExpirationValue());
        $this->addItem($item);
    }
}
