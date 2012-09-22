<?php

/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Detector
 *
 * @copyright (C) 2008 - 2012 Alikonweb. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.alikonweb.it
 * */
defined('_JEXEC') or die();

jimport('joomla.utilities.string');

class KunenaActivityDetector extends KunenaActivity {

    protected $params = null;
    protected $testo = null;
    protected $db = null;
    protected $user = null;
    protected $app = null;
    protected $baseurl = null;
    protected $cfg = null;
    protected $mailfrom = null;
    protected $fromname = null;

    public function __construct($params) {
        $this->params = $params;
        $this->db = JFactory::getDBO();
        $this->user = & JFactory::getUser();
        $this->app = JFactory::getApplication();
        $this->cfg = JFactory::getConfig();
        $this->mailfrom = $this->cfg->get('mailfrom');
        $this->fromname = $this->cfg->get('fromname');
    }

    //jexit('dopoact' . var_dump($message));
    public function onAfterPost($message) {
        $this->detect($message);
        return true;
    }

    public function onAfterReply($message) {
        $this->detect($message);
        return true;
    }

    function detect($message) {

        $result = $this->investigate($message);
        /* @var $result type */
        if ($result) {
            $this->process($message);
        }
        return $this->app->enqueueMessage('Detected', 'Notice');
    }

    function investigate($message) {

        $ris = false;

        $messaggio = $message->get('message');
        
        $forumUrl = JURI::base() . 'index.php?option=com_kunena';
        JPluginHelper::importPlugin('alikonweb', 'alikonweb.detector');
        $dispatcher = JDispatcher::getInstance();

          if ($this->params->get('checktype', '0')) {
              $info_detector = $dispatcher->trigger('onDetectText', array(null, $this->user->get('email'), $this->user->get('name'), $messaggio, $forumUrl));
          }else {
              $info_detector = $dispatcher->trigger('onDetectFull', array(null, $this->user->get('email'), $this->user->get('name'), $messaggio, $forumUrl));              
          }                  
        // jexit('score:'.$info_detector[0]['text']);

        if ($info_detector[0]['score'] >= 4) {
            $ris = true;
            $this->testo = $info_detector[0]['text'];
        } else {
            $ris = false;
            $this->testo = $info_detector[0]['text'];
        }
        ////$this->app->enqueueMessage('investigate:' . $this->testo, 'warning');
        return $ris;
    }

    public function process($message) {


        if ((int) $this->params->get('hidemessage', '0') > 0) {

            $this->hidepost($message);
        }
        if ($this->params->get('blockspammer', '0')) {

            $this->blockuser($message);
        }
        if ($this->params->get('sendmail2mod', '0')) {

            $this->sendmail2mod($message);
        }
        if ($this->params->get('sendmail2usr', '0')) {

            $this->sendmail2usr($message);
        }
        if ($this->params->get('userban', '0')) {

            $this->banuser($message);
        }
        if ($this->params->get('logoffspammer', '0')) {

            $this->logoffuser($message);
        }


        //// return $this->app->enqueueMessage('process:' . $this->testo, 'warning');
        return true;
    }

    public function banuser($message) {
        $ip = '99.99.99.99';
        $block = 0;
        
        $now = new JDate();
        $jnow= new JDate();
        $ora = $now->toMysql();
     
        $now = substr($jnow->toMySQL(), 0, 10);
        $now = $jnow->toMySQL();
        $extend=30;

        $reason_private = 'Spam';
        $reason_public = 'Spam';
        $comment = '';


        $sql = 'INSERT INTO #__kunena_users_banned VALUES (NULL, ' . $message->get('userid') .
                ' , ' . $this->db->Quote($ip) .
                ' , ' . $block .
                ' , ADDDATE(' . $this->db->Quote($now) . ', ' . $this->db->Quote($extend) . ')' .
                ' , ' . 42 .
                ' , ' . $this->db->Quote($ora) .
                ' , ' . $this->db->Quote($reason_private) .
                ' , ' . $this->db->Quote($reason_public) .
                ', NULL , NULL , NULL , NULL)';
        $this->db->setQuery($sql);
        $this->db->query();
        if ($this->db->getErrorNum()) {
            JError::raiseError(290, $this->db->getErrorMsg());
        }

        $sql = 'UPDATE #__kunena_users SET banned = ' . $this->db->Quote($ora) . '  WHERE userid=' . $message->get('userid');
        $this->db->setQuery($sql);
        $this->db->query();
        if ($this->db->getErrorNum()) {
            JError::raiseError(291, $this->db->getErrorMsg());
        }
        return true;
    }

    public function hidepost($message) {
        if ((int) $this->params->get('hidemessage', '0') == 1) {
            $sql = 'UPDATE #__kunena_messages SET hold = 1, modified_by =68, modified_reason =' . $this->db->Quote($this->testo) . ' WHERE id=' . $message->get('id');
        }
        if ((int) $this->params->get('hidemessage', '0') == 2) {
            $sql = 'UPDATE #__kunena_messages SET hold = 1, modified_by =68, modified_reason =' . $this->db->Quote($this->testo) . ' WHERE userid=' . $message->get('userid');
        }
        $this->db->setQuery($sql);
        $this->db->query();
        if ($this->db->getErrorNum()) {
            JError::raiseError(292, $this->db->getErrorMsg());
        }

        return true;
    }

    public function blockuser($message) {

        $this->user->block = 1;
        $this->user->save();
        ////return $this->app->enqueueMessage('blockuser:' . $this->testo, 'error');
        return true;
    }

    public function logoffuser($message) {
        $this->app->logout();
        ////return $this->app->enqueueMessage('logoff:' . $this->testo, 'warning');
        return true;
    }

    public function sendmail2usr($message) {
        $soggetto = '[Detector Kunena Forum] ' . $message->get('subject') . ' [SPAM WARN]';
        $mbody = JText::sprintf(
                        'PLG_KUNENA_SUBMSSION_PENDING', $message->get('message')
        );
        $this->_mandamail($this->user->get('email'), $soggetto, $mbody);
        ////return $this->app->enqueueMessage('sendmail2usr' . $this->testo, 'notice');
        return true;
    }

    public function sendmail2mod($message) {

        $soggetto = '[Detector Kunena Forum] ' . $message->get('subject') . ' [SPAM WARN]';
        $pendingurl = JRoute::_(JURI::base() . 'index.php?option=com_kunena&func=review&action=list&catid=' . $message->get('catid'), false);
        $mbody = JText::sprintf(
                        'PLG_KUNENA_EMAIL_PENDING_BODY', $message->get('message'), $message->get('subject'), $message->get('name'), $this->testo, $pendingurl
        );

        $testo = $message->get('message');
        $query = 'SELECT name, email, sendEmail' .
                ' FROM #__kunena_users , #__users' .
                ' WHERE moderator =1' .
                ' AND userid = id';
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

        $mail = JFactory::getMailer()->
                setSender(array($this->mailfrom, $this->fromname))->
                addRecipient($anome)->
                setSubject($soggetto)->
                setBody($testo);
        // jexit('s:'.$dasito.' dn:'.$danome.' an:'.$anome.' s:'.$soggetto.' t:'.$testo);
        if (!$mail->Send()) {
            // 

            return $this->app->enqueueMessage($dasito . ' ' . $this->mailfrom . ' ' . $this->fromname . ' ' . $soggetto . ' ' . $testo, 'warning');
        }
        return true;
    }

}
