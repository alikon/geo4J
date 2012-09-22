<?php

/**
 * @version		$Id: detector.php 11/05/2011 20.49
 * @package		Joomla
 * @subpackage	JFramework
 * @copyright	Copyright (C) 2005 - 2011 Alikonweb.it All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Example User Plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since		1.5
 */
class plgUserDetector extends JPlugin {

    /**
     * Example store user method
     *
     * Method is called before user data is stored in the database
     *
     * @param	array		$user	Holds the old user data.
     * @param	boolean		$isnew	True if a new user is stored.
     * @param	array		$new	Holds the new user data.
     *
     * @return	void
     * @since	1.6
     * @throws	Exception on error.
     */
    var $db = null;
    var $app = null;

    public function __construct(& $subject, $config) {
        $this->db = JFactory::getDbo();
        $this->app = JFactory::getApplication();
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onUserBeforeSave($user, $isnew, $new) {

        // $lang = JFactory :: getLanguage();
        // $lang->load('plg_user_detector', JPATH_ADMINISTRATOR);
        //  jexit('userdetector:onUserBeforeSave');
        if ($isnew) {
            // Call a function in the external app to create the user
            // ThirdPartyApp::createUser($user['id'], $args);
            if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
                JPluginHelper::importPlugin('alikonweb');
                $dispatcher2 = JDispatcher::getInstance();
                $info_detector = $dispatcher2->trigger('onDetectUser', array(null, $user['email'], $user['username'], ' ', ' '));
                // jexit(var_dump($info_detector));
                if ($info_detector[0]['score'] > 4) {
                    //return JError::raiseError(401, JText::_('DENIED_FOR_SPAM').$info_detector[0]['text'].$info_detector[0]['score']);
                    JError::raiseWarning(401, JText::_('DETECTD_SPAM'));
                    $this->app->enqueueMessage(JText::_('COM_USERS_SUBMSSION_FAILED_XSPAM') . $info_detector[0]['text']);
                    $this->app->Redirect(JRoute::_('index.php?option=com_users', false));
                    return false;
                }
            }
        }
    }

    /**
     * Example store user method
     *
     * Method is called after user data is stored in the database
     *
     * @param	array		$user		Holds the new user data.
     * @param	boolean		$isnew		True if a new user is stored.
     * @param	boolean		$success	True if user was succesfully stored in the database.
     * @param	string		$msg		Message.
     *
     * @return	void
     * @since	1.6
     * @throws	Exception on error.
     */
    public function onUserAfterSave($user, $isnew, $success, $msg) {

        // jexit('userdetector:onUserAfterSave');
        // convert the user parameters passed to the event
        // to a format the external application
        if ($isnew) {
            $args = array();
            $args['username'] = $user['username'];
            $args['email'] = $user['email'];
            $args['fullname'] = $user['name'];
            $args['password'] = $user['password'];
            if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
                JPluginHelper::importPlugin('alikonweb');
                $dispatcher2 = JDispatcher::getInstance();
                $info_detector = $dispatcher2->trigger('onDetectUser', array(null, $user['email'], $user['username'], ' ', ' '));
                if ($this->_aa4jisenabled()) {
                    $this->_insertextras($user, $info_detector);
                }
            }
        }
    }

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @param	array	$user		Holds the user data.
     * @param	array	$options	Extra options.
     *
     * @return	boolean	True on success
     * @since	1.5
     */
    public function onUserLogin($user, $options) {
        // Initialise variables.
        $success = true;
        if ($this->_aa4jisenabled()) {
            $instance = JUser::getInstance();
            if ($id = intval(JUserHelper::getUserId($user['username']))) {
                $instance->load($id);
                $this->_checkextras($instance->get('id'));
            }
        }
        return $success;
    }

    /**
     * Example store user method
     *
     * Method is called after user data is deleted from the database
     *
     * @param	array		$user	Holds the user data.
     * @param	boolean		$succes	True if user was succesfully stored in the database.
     * @param	string		$msg	Message.
     *
     * @return	void
     * @since	1.6
     */
    public function onUserAfterDelete($user, $succes, $msg) {


        // only the $user['id'] exists and carries valid information
        // Call a function in the external app to delete the user
        // ThirdPartyApp::deleteUser($user['id']);
        if ($this->_aa4jisenabled()) {
            $this->_deleteextras($user['id']);
        }
    }

    private function _aa4jisenabled() {


        $query = $this->db->getQuery(true);
        $query->select('extension_id AS id, element AS "option", params, enabled');
        $query->from('#__extensions');
        $query->where($query->qn('type') . ' = ' . $this->db->quote('component'));
        $query->where($query->qn('element') . ' = ' . $this->db->quote('com_aa4j'));
        $this->db->setQuery($query);
        $comp = $this->db->loadObject();
        if (!$this->db->query()) {
            JError::raiseError(392, $this->db->getErrorMsg());
        }
        return $comp->enabled;
    }

    function _checkextras($userid) {
//to redo
        $this->db->setQuery(
                'select * FROM #__userextras WHERE id=' . (int) $userid
        );
        $id = $this->db->loadResult();

        if ($this->db->getErrorNum()) {
            JError::raiseNotice(500, $this->db->getErrorMsg());
            return false;
        }
        if ($id) {
            $this->_updateextras($userid);
        } else {
            if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
                JPluginHelper::importPlugin('alikonweb');
                $dispatcher2 = JDispatcher::getInstance();
                $info_detector = $dispatcher2->trigger('onDetectUser', array(null, 'email', 'username', ' ', ' '));
                // Call a function in the external app to create the user
                $user['id'] = $userid;
                $this->_insertextras($user, $info_detector);
            }
        }
    }

    function _updateextras($userid) {

        $this->db->setQuery(
                'UPDATE #__userextras SET nvisit = nvisit +  1 WHERE id=' . (int) $userid
        );
        $this->db->query();

        if ($this->db->getErrorNum()) {
            JError::raiseNotice(500, $this->db->getErrorMsg());
            return false;
        }
    }

    function _deleteextras($userid) {

        $this->db->setQuery(
                'DELETE FROM #__userextras WHERE id=' . (int) $userid
        );
        $this->db->query();

        if ($this->db->getErrorNum()) {
            JError::raiseNotice(500, $this->db->getErrorMsg());
            return false;
        }
    }

    function _insertextras($user, &$info_detector) {

        $extend = 60;
        $spazio = "";
        $jnow = & JFactory::getDate();
        $now = substr($jnow->toMySQL(), 0, 10);
        $now = $jnow->toMySQL();
        $this->db->setQuery(
                'INSERT INTO #__userextras VALUES ( ' . (int) $user['id'] . ' , 1 , 0, 0, ADDDATE("' . $now . '", ' . $extend . '),' . $this->db->Quote($info_detector[0]['ip']) .
                ',' . $this->db->Quote($info_detector[0]['city']) . ',' . $this->db->Quote($info_detector[0]['country_code']) . ',' . $this->db->Quote($info_detector[0]['country_name']) . ',' . $this->db->Quote($info_detector[0]['latitude']) .
                ',' . $this->db->Quote($info_detector[0]['longitude']) . ',' . $this->db->Quote($spazio) . ',' . $this->db->Quote($spazio) . ',' . $this->db->Quote($spazio) . ',0 )'
        );

        $this->db->query();

        if ($this->db->getErrorNum()) {
            JError::raiseNotice(500, $this->db->getErrorMsg());
            return false;
        }
    }

}