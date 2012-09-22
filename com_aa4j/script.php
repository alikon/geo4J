<?php

/**
 * @version		$Id: script.php 26/02/2012 17.41
 * @package		Joomla!2.5
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2012 alikonweb, Inc. All rights reserved.
 * @author		alikon
 * @based on	http://joomlacode.org/gf/project/com_helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of aa4j component
 */
class Com_aa4jInstallerScript {

    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent) {
        echo '<p>' . JText::_('COM_AA4J_INSTALL_TEXT') . '</p>';
        // $parent is the class calling this method
        $parent->getParent()->setRedirectURL('index.php?option=com_aa4j');
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent) {
        // $parent is the class calling this method
        echo '<p>' . JText::_('COM_AA4J_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent) {
        // $parent is the class calling this method
        echo '<p>' . JText::_('COM_AA4J_UPDATE_TEXT') . '</p>';
        $parent->getParent()->setRedirectURL('index.php?option=com_aa4j');
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    // $parent is the class calling this method
    // $type is the type of change (install, update or discover_install)
    public function preflight($type, $adapter) {
        if ($type !== 'update')
            return true;

        // "Fix" Joomla! bug
        $row = JTable::getInstance('extension');
        $eid = $row->find(array('element' => strtolower($adapter->get('element')), 'type' => 'component'));

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('version_id')
                ->from('#__schemas')
                ->where('extension_id = ' . $eid);
        $db->setQuery($query);

        if ($db->loadResult())
            return true;

        // Get the previous version
        $old_manifest = null;
        // Create a new installer because findManifest sets stuff
        // Look in the administrator first
        $tmpInstaller = new JInstaller;
        $tmpInstaller->setPath('source', JPATH_ADMINISTRATOR . '/components/com_aa4j');

        if (!$tmpInstaller->findManifest()) {
            echo 'Could not find old manifest.';
            return false;
        }

        $old_manifest = $tmpInstaller->getManifest();
        $version = (string) $old_manifest->version;

        // Store
        $data = new stdClass;
        $data->extension_id = $eid;
        $data->version_id = $version;
        $db->insertObject('#__schemas', $data);
        echo '<p>' . JText::_('COM_AA4J_PREFLIGHT_' . $type . '_TEXT') . '</p>';
        return true;
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        $app = JFactory::getApplication();
        echo '<p>' . JText::_('COM_AA4J_POSTFLIGHT_TEXT'). '</p>';
    }

}
