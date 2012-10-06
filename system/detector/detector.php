<?php

/**
 * detector4kunena system plugin
 * Embedd a spam report on Joomla! Kunena component
 * 
 * @author: Alikon
 * @version: 1.7.0
 * @release: 18/09/2011 17.03
 * @package: Alikonweb.detector 4 Kunena
 * @copyright: (C) 2007-2011 Alikonweb.it
 * @license: http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 *
 *
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemDetector extends JPlugin {

    /**
     * Constructor function
     *
     * @param object $subject
     * @param object $config
     * @return plgSystemDetector4kunena
     */
    var $cfg = null;
    var $mailfrom = null;
    var $fromname = null;
    var $app = null;
    var $db = null;
    var $lang = null;
    var $user = null;
    var $testo = null;

    function __construct(&$subject, $config) {
        $this->app = JFactory::getApplication();

        $this->cfg = JFactory::getConfig();
        $this->mailfrom = $this->cfg->get('mailfrom');
        $this->fromname = $this->cfg->get('fromname');
        $this->db = JFactory::getDbo();
        parent::__construct($subject, $config);
        $this->lang = JFactory :: getLanguage();
        $this->lang->load('plg_system_detector', JPATH_ADMINISTRATOR);
    }

    function onAfterRoute() {
        if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
            if ($this->app->isAdmin()) {
                $this->detect4back();
            } else {
                $this->detect4front();
            }
        }
    }

    /**
     * Use this to check the spam
     *
     * @return boolean
     */
    //
    private function detect4back() {
        $option = JRequest::getVar('option');
        $task = JRequest::getVar('task');

        if ($task == 'detecttask') {

            switch ($option) {
                case 'com_content':
                    $this->chkContents();

                    break;
                case 'com_users':
                    $this->chkUsers();

                    break;
                case 'com_weblinks':
                    $this->chkWeblinks();
                    break;
                case 'com_slicomments':
                    $this->chkSlicomments();
                    break;
                default:
                    $this->app->enqueueMessage(JText::_('COM_NOTSUPPORTED'));
                    break;
            }
        }
        // JError::raiseNotice(102, ' checked by Detector plugin ');
        return true;
    }

    private function detect4front() {
        //Get the plugin
        //Define parameters	

        $this->user = & JFactory::getUser();
        if ($this->user->get('guest') == 1) {
            $this->user->set('email', 'a@a.com');
            $this->user->set('name', 'guest');
        }

        $option = JRequest::getCmd('option', '');
        $task = JRequest::getCmd('task', '');
        if (($option == 'com_weblinks') && ($task == 'weblink.save')) {
            $datiform = JRequest::getVar('jform');
            //var_dump(	$datiform['description']);
            JPluginHelper::importPlugin('alikonweb');
            $dispatcher = JDispatcher::getInstance();
            if ($this->params->get('checktype', '0')) {
                $info_detector = $dispatcher->trigger('onDetectText', array(null, $this->user->get('email'), $this->user->get('name'), $datiform['description'], $datiform['url']));
            } else {
                $info_detector = $dispatcher->trigger('onDetectFull', array(null, $this->user->get('email'), $this->user->get('name'), $datiform['description'], $datiform['url']));
            }

            if ($info_detector[0]['score'] >= 4) {
                if (!$this->user->get('guest') == 1) {
                    if ($this->params->get('sendmail2adm', '0')) {
                        $this->testo = $info_detector[0]['text'];
                        $this->sendmail2adm($datiform['description'], $datiform['url']);
                    }
                    if ($this->params->get('blockspammer', '0')) {
                        $this->blockuser();
                    }
                    if ($this->params->get('logoffspammer', '0')) {
                        $this->logoffuser();
                    }
                }
                $this->app->enqueueMessage(JText::_('COM_WEBLINKS_SUBMSSION_FAILED_XSPAM') . $info_detector[0]['text']);
                $this->app->Redirect(JRoute::_('index.php?option=com_weblinks', false));
            }
            JError::raiseNotice(102, ' checked by Detector plugin ' . $info_detector[0]['text']);
        }
        if (($option == 'com_content') && ($task == 'article.save')) {
            $datiform = JRequest::getVar('jform');
            //var_dump(	$datiform['description']);
            JPluginHelper::importPlugin('alikonweb');
            $dispatcher = JDispatcher::getInstance();
            if ($this->params->get('checktype', '0')) {
                $info_detector = $dispatcher->trigger('onDetectText', array(null, $this->user->get('email'), $this->user->get('name'), $datiform['articletext'], ''));
            } else {
                $info_detector = $dispatcher->trigger('onDetectFull', array(null, $this->user->get('email'), $this->user->get('name'), $datiform['articletext'], ''));
            }
            //var_dump(	JRequest::getVar('jform'));Jexit();
            //return	JError::raiseWarning(392, 'dplug->d:'. $datiform['description'].' - o:'.$option.' - t:'.$task);	
            //JError::raiseNotice( 102, ' checked by Detector plugin '.$info_detector[0]['text'] );	 
            if ($info_detector[0]['score'] >= 4) {
                if (!$this->user->get('guest') == 1) {
                    if ($this->params->get('sendmail2adm', '0')) {
                        $this->testo = $info_detector[0]['text'];
                        $this->sendmail2adm($datiform['articletext'], $datiform['title']);
                    }
                    if ($this->params->get('blockspammer', '0')) {
                        $this->blockuser();
                    }
                    if ($this->params->get('logoffspammer', '0')) {
                        $this->logoffuser();
                    }
                }
                $this->app->enqueueMessage(JText::_('COM_CONTENT_SUBMSSION_FAILED_XSPAM') . $info_detector[0]['text']);
                $this->app->Redirect(JRoute::_('index.php?option=com_content', false));
            }
            JError::raiseNotice(102, ' checked by Detector plugin ' . $info_detector[0]['text']);
        }

        return;
    }

    function chkUsers() {

        $cids = JRequest::getVar('cid', array(0), 'post', 'array');

        if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
            JPluginHelper::importPlugin('alikonweb');
            $dispatcher2 = JDispatcher::getInstance();
            foreach ($cids as $cid) {

                $this->db->setQuery(
                        'select * FROM #__userextras e , #__users u WHERE e.id=' . (int) $cid
                        . ' and u.id=e.id'
                );

                $user = $this->db->loadObject();
                if (!$this->db->query()) {
                    JError::raiseError(392, $this->db->getErrorMsg());
                }
                //jexit(var_dump($user));
                $info_detector = $dispatcher2->trigger('onDetectUser', array($user->ip, $user->email, $user->username, ' ', ' '));
                if ($info_detector[0]['score'] >= 4) {
                    $this->db->setQuery(
                            'UPDATE #__users SET block =   1 WHERE id=' . (int) $cid
                    );
                    $this->db->query();

                    if ($this->db->getErrorNum()) {
                        JError::raiseNotice(500, $this->db->getErrorMsg());
                        return false;
                    }
                }
                $this->app->enqueueMessage($cid . JText::_('COM_USERS_checkedD_XSPAM') . $user->ip . $info_detector[0]['text']);
            }
        }


        $this->app->Redirect(JRoute::_('index.php?option=com_users', false));
    }

    function chkContents() {

        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        // $row =& $this->getTable();
        if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
            JPluginHelper::importPlugin('alikonweb');
            $dispatcher2 = JDispatcher::getInstance();
            foreach ($cids as $cid) {
                $query = 'select c.id,c.title,c.introtext,c.fulltext ,IFNULL(u.username,' .
                        $this->db->quote('guest') . ') AS username ' .
                        ', IFNULL(u.email,' . $this->db->quote('guest@guest.com') . ') AS email ' .
                        ' FROM #__content c LEFT JOIN #__users u ON u.id=c.created_by WHERE c.id=' . (int) $cid;
                $this->db->setQuery($query);

                $user = $this->db->loadObject();
                //echo(var_dump($user));
                if (!$this->db->query()) {
                    JError::raiseError(392, $this->db->getErrorMsg());
                }
                //jexit(var_dump($user));
                if ($this->params->get('checktype', '0')) {
                    $info_detector = $dispatcher2->trigger('onDetectText', array(null, $user->email, $user->username, $user->introtext . $user->fulltext, ' '));
                } else {
                    $info_detector = $dispatcher2->trigger('onDetectFull', array(null, $user->email, $user->username, $user->introtext . $user->fulltext, ' '));
                }
                // jexit(var_dump($info_detector));
                if ($info_detector[0]['score'] >= 4) {
                    $this->db->setQuery(
                            'UPDATE #__content SET state = 0 WHERE id=' . (int) $cid
                    );
                    $this->db->query();
//jexit('qui');
                    if ($this->db->getErrorNum()) {
                        JError::raiseNotice(500, $this->db->getErrorMsg());
                        return false;
                    }
                }

                $this->app->enqueueMessage(JText::sprintf('COM_CONTENT_FAILED_XSPAM', $user->title, $cid, $info_detector[0]['text']));
            }
        }


        $this->app->Redirect(JRoute::_('index.php?option=com_content', false));
    }

    function chkWeblinks() {

        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        // $row =& $this->getTable();
        if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
            JPluginHelper::importPlugin('alikonweb');
            $dispatcher2 = & JDispatcher::getInstance();
            foreach ($cids as $cid) {
                $query = 'select w.id,w.title,w.description, IFNULL(u.username,' . $this->db->quote('guest') . ') AS username,' .
                        ' IFNULL(u.email,' . $this->db->quote('guest@guest.com') . ') AS email ' .
                        'FROM #__weblinks w LEFT JOIN #__users u ON u.id=w.created_by WHERE w.id=' . (int) $cid;
                $this->db->setQuery($query);

                $user = $this->db->loadObject();
                //echo(var_dump($user));
                if (!$this->db->query()) {
                    JError::raiseError(392, $this->db->getErrorMsg());
                }
                if ($this->params->get('checktype', '0')) {
                    $info_detector = $dispatcher2->trigger('onDetectText', array(null, $user->email, $user->username, $user->title . $user->description, ' '));
                } else {
                    $info_detector = $dispatcher2->trigger('onDetectFull', array(null, $user->email, $user->username, $user->title . $user->description, ' '));
                }
                // jexit(var_dump($info_detector));
                if ($info_detector[0]['score'] >= 4) {
                    $this->db->setQuery(
                            'UPDATE #__weblinks SET state = 0 WHERE id=' . (int) $cid
                    );
                    $this->db->query();
//jexit('qui');
                    if ($this->db->getErrorNum()) {
                        JError::raiseNotice(500, $this->db->getErrorMsg());
                        return false;
                    }
                }
                $this->app->enqueueMessage(JText::sprintf('COM_WEBLINKS_FAILED_XSPAM', $user->title, $cid, $info_detector[0]['text']));
            }
        }


        $this->app->Redirect(JRoute::_('index.php?option=com_weblinks', false));
    }

    function chkSlicomments() {

        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        // $row =& $this->getTable();
        if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
            JPluginHelper::importPlugin('alikonweb');
            $dispatcher2 = & JDispatcher::getInstance();
            foreach ($cids as $cid) {
                $query = 'select name AS username, raw AS title, text AS description,' .
                        ' IFNULL(email,' . $this->db->quote('guest@guest.com') . ') AS email ' .
                        'FROM #__slicomments WHERE id=' . (int) $cid;
                $this->db->setQuery($query);

                $user = $this->db->loadObject();
               //echo(var_dump($user->description));
                if (!$this->db->query()) {
                    JError::raiseError(392, $this->db->getErrorMsg());
                }
                if ($this->params->get('checktype', '0')) {
                    $info_detector = $dispatcher2->trigger('onDetectText', array(null, $user->email, $user->username, $user->description, ' '));
                } else {
                    $info_detector = $dispatcher2->trigger('onDetectFull', array(null, $user->email, $user->username, $user->description, ' '));
                }
                // jexit(var_dump($info_detector));
                if ($info_detector[0]['score'] >= 4) {
                    $this->db->setQuery(
                            'UPDATE #__slicomments SET status = -1 WHERE id=' . (int) $cid
                    );
                    $this->db->query();
//jexit('qui');
                    if ($this->db->getErrorNum()) {
                        JError::raiseNotice(500, $this->db->getErrorMsg());
                        return false;
                    }
                }
                $this->app->enqueueMessage(JText::sprintf('COM_SLICOMMENTS_FAILED_XSPAM', $user->username.'('.$user->email .')', $cid, $info_detector[0]['text']));
            }
        }


        $this->app->Redirect(JRoute::_('index.php?option=com_slicomments', false));
    }

    /**
     * Method is called by index.php and administrator/index.php
     *
     * @access	public
     */
    public function onAfterDispatch() {

        if ($this->app->getName() != 'administrator') {
            return true;
        }
        // Do not load if Alikonweb Detector is not installed
        if (!(JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'detector' . DS . 'detector.php')))
            return;
        // Do not load if Alikonweb Detector is not enabled
        if (!(JPluginHelper::isEnabled('alikonweb', 'detector')))
            return;
        $option = JRequest::getVar('option');
        $section = JRequest::getVar('section');
        $task = JRequest::getVar('task');
        $layout = JRequest::getVar('layout');
        $toolbar = JToolBar::getInstance('toolbar');
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
        if ($comp->enabled) {
            $disponibile = array('com_users', 'com_weblinks', 'com_content', 'com_slicomments');
        } else {
            $disponibile = array('com_weblinks', 'com_content', 'com_slicomments');
        }

        if ((in_array($option, $disponibile)) && ($layout == '')) {
            $url = JRoute::_('index.php?option=' . $option . '&task=detecttask&format=raw');
            $toolbar->appendButton('Standard', 'refresh', 'Detect', 'detecttask', true);
        }
        return true;
    }

    public function logoffuser() {
        $error = $this->app->logout();
        $url = 'index.php?option=com_content&view=article&id=8';
        $url = JRoute::_($url, true);
        $msg = JText::_('DETECTED_AS_SPAM');
        $this->app->redirect($url, $msg, 'error');
        return true;
    }

    public function blockuser() {

        $this->user->block = 1;
        $this->user->save();

        return true;
    }

    public function sendmail2adm($message, $url) {

        $soggetto = '[Detector 4 Joomla] ' . $url . ' [SPAM WARN]';

        $mbody = JText::sprintf(
                        'PLG_JOOMLA_EMAIL_PENDING_BODY', $message, $this->user->get('name'), $this->testo
        );


        $query = 'SELECT name, email, sendEmail' .
                ' FROM #__user_usergroup_map , #__users' .
                ' WHERE user_id =id' .
                ' AND group_id = 8';
        $this->db->setQuery($query);
        if (!$this->db->query()) {
            JError::raiseError(392, $this->db->getErrorMsg());
        }
        $mods = $this->db->loadObjectList();
        foreach ($mods as $mod) {
            $this->_mandamail($mod->email, $soggetto, $mbody);
        }
        ////return $this->app->enqueueMessage('sendmail2mod' . $this->testo, 'warning');
        return true;
    }

    function _mandamail($anome, $soggetto, $testo) {
// Assemble the email data...the sexy way!
        //  jexit('mail:'.$anome.' - '.$soggetto.' - '.$testo);
        $mail = JFactory::getMailer()->
                setSender(array($this->mailfrom, $this->fromname))->
                addRecipient($anome)->
                setSubject($soggetto)->
                setBody($testo);

        if (!$mail->Send()) {

            return $this->app->enqueueMessage('warning mail not sended');
        }
        return true;
    }

}