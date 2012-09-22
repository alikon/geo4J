<?php

/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Joomla Detector
 *
 * @Copyright (C) 2008 - 2012 alikon. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.alikonweb.it
 * */
defined('_JEXEC') or die();

class plgKunenaDetector extends JPlugin {

    public function __construct(&$subject, $config) {
        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') && KunenaForum::installed()))
            return;
         // Do not load if Alikonweb Detector is not installed
        if (!(JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'detector' . DS . 'detector.php')))
            return;
          // Do not load if Alikonweb Detector is not enabled
        if (!(JPluginHelper::isEnabled('alikonweb', 'detector')))
            return;

        parent::__construct($subject, $config);

        $this->loadLanguage('plg_kunena_detector.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_detector.sys', KPATH_ADMIN);

        $this->path = dirname(__FILE__) . '/detector';
    }

    /*
     * Get Kunena activity stream integration object.
     *
     * @return KunenaActivity
     */

    public function onKunenaGetActivity() {
        if (!$this->params->get('activity', 1))
            return;
        /*
         * devo sistemare sql
         * 
         * 
         * 
         */

        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            return;
        }
        // Check if I am a Super Admin
        $my = JFactory::getUser();
        $iAmSuperAdmin = $my->authorise('core.admin');
        if ($iAmSuperAdmin) {
            return;
        }

        // devo escludere superadmin e moderator
        //Get the DB
        $db = JFactory::getDBO();
        $query = 'SELECT userid' .
                ' FROM #__kunena_users ' .
                ' WHERE moderator =1' .
                ' AND userid = ' . $my->get('id');
        $db->setQuery($query);
        if ($db->getErrorNum()) {
            JError::raiseError(292, $db->getErrorMsg());
        }
        $iAmModerator = $db->loadResult();
        if ($iAmModerator) {
            return;
        }

        require_once "{$this->path}/activity.php";

        return new KunenaActivityDetector($this->params);
    }

}
