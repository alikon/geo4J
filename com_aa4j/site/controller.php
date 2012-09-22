<?php
/**
 * @version		$Id: controller.php 25/02/2012 12.56
 * @package		Alikonweb ajax 4 Joomla 1.6 Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 alikonweb.it, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Base controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.5
 */
class Aa4jController extends JController {

    function deleteUser() {
        $user = JFactory::getUser();
        $response = array();
        $app = JFactory::getApplication();
        $response['msg'] = 'false';
        $username = $user->get('username');
        $response['html'] = 'Deleted account for ' . $user->get('username');

        $iAmSuperAdmin = $user->authorise('core.admin');
        if ($iAmSuperAdmin != true) {
            $options = array();
            $options['clientid'][] = 0; // site 
            //$app->logout( (int) $user->get('id'), $options); 
            $user->delete();
            $app->logout();
            $response['msg'] = 'true';
            $response['html'] = $username . ': Your account has been deleted';
        } else {
            $response['msg'] = 'false';
            $response['html'] = 'Cannot delete superadmin:' . $user->get('username');
        }
        echo (json_encode($response));

        return;
    }

    /* working on Ajax captcha */

    function chkCaptcha() {
        //	$usersConfig = &JComponentHelper::getParams( 'com_auser' );
        //alikonweb
        //if ( $usersConfig->get( 'alikonweb_secureplg_login') ) {

        if (JPluginHelper::isEnabled('alikonweb', 'acaptcha')) {
            // do a Captcha safety check
            $campo = JRequest::getVar('campo', '', 'get', 'cmd');
            $psw3 = JRequest::getVar($campo, '', 'get', 'cmd');
            $buttonid = JRequest::getVar('fid', '', 'get', 'cmd');
            $psw3 = strtolower($psw3);

            JPluginHelper::importPlugin('alikonweb');
            $dispatcher = & JDispatcher::getInstance();
            $results = $dispatcher->trigger('onVerify', array($psw3, $buttonid));

            if ($results[0] == 0) {
                $response['msg'] = 'false';
                $response['html'] = '<div><p><b>' . $psw3 . '</b> ' . JText::_('TOO MANY FAILED ATTEMPTS') . '</p></div>';
                //$response['msg'] = JText::_('SECURECODE_DO_NOT_MATCH');
                echo (json_encode($response));

                return;
            }

            if ($results[0] == 1) {
                $response['msg'] = 'false';
                $response['html'] = '<div><p><b>' . $psw3 . '</b> ' . JText::_('SECURECODE_DO_NOT_MATCH') . '</p></div>';
                //$response['msg'] = JText::_('SECURECODE_DO_NOT_MATCH');
                echo (json_encode($response));
            } else {
                $response['msg'] = 'true';
                //$response['msg'] = JText::_('SECURECODE_MATCH');
                $response['html'] = '<div><p>' . JText::_('SECURECODE_MATCH') . '</p></div>';
                echo (json_encode($response));
            }
        }
        //}

        return;
    }

    //19/03/2010 20.48
    function viewCaptacha() {
        global $mainframe;
        JPluginHelper::importPlugin('alikonweb');
        $dispatcher = & JDispatcher::getInstance();
        //$results = $dispatcher->trigger( 'onView',array('experiment','auser','submittest','pswtest','spantest' ) );		
        $results = $dispatcher->trigger('onView2', array('com', 'auser', 'submittest', 'pswtest', 'spantest', 'refreshidtest', 'capthcaformtest', 'captchaidtest'));
        // Redirect to a login form
        $response['msg'] = 'true';
        $response['html'] = $results;
        echo (json_encode($response));
        return;
    }

    function refreshCaptacha() {
        global $mainframe;
        $buttonid = JRequest::getVar('fid', '', 'get', 'cmd');
        JPluginHelper::importPlugin('alikonweb');
        $dispatcher = & JDispatcher::getInstance();
        $response['msg'] = 'true';
        $response['html'] = $dispatcher->trigger('onRefresh', array('com', $buttonid));
        echo (json_encode($response));
        return;
    }

    //

    public function chkIP() {
        global $mainframe;
        $userip = JRequest::getVar('testip', '', 'get', 'cmd');
        $msg = '';

        if (!$userip) {
            $userip = $this->getUserIpAddr();
        }
        if (!$this->IsvalidIP($userip)) {
            $response['msg'] = 'false';
            $response['html'] = '<div  style="visibility: visible; opacity: 1;">' . JText::_('Invalid ip') . '</div>';
            echo (json_encode($response));
            return;
        }


        //alikonweb	
        //	if ( $cparams->get( 'show_secure_code',true) ) {  		  
        if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
            JPluginHelper::importPlugin('alikonweb');
            $dispatcher = & JDispatcher::getInstance();
            $info_detector = $dispatcher->trigger('onDetectUser', array(null, 'pippo@pippo.it', 'nome', ' ', ' '));
            //   $msg.='Status:'. $info_detector[0]['status'];
            //   if ($info_detector[0]['status']=='ok'){	
            $msg.='<b>Country:</b> (' . $info_detector[0]['country_code'] . ')' . $info_detector[0]['country_name'] . '&nbsp;';
            $msg.='<b>City:</b>' . $info_detector[0]['city'] . '<br>';
            $msg.='latitude:' . $info_detector[0]['latitude'] . '<br>';
            $msg.='longitude:' . $info_detector[0]['longitude'] . '<br>';
            //	 }	 
            $msg.='ip:' . $info_detector[0]['ip'] . '<br>';
            //$msg.='Score:'. $info_detector[0]['score'];
            $msg.='Score:' . $info_detector[0]['text'];
            $msg.='Score2:' . $info_detector[0]['score'];


            $response['msg'] = 'true';
            $response['html'] = '<div style="visibility: visible; opacity: 1;">' . $msg . '</div>';

            echo (json_encode($response));
            return;
            // do a Captcha safety check      
        } else {
            $response['msg'] = 'false';
            $response['html'] = '<div  style="visibility: visible; opacity: 1;">' . JText::_('Detector not installed') . '</div>';
            echo (json_encode($response));
            return;
        }
    }

    function IsvalidIP($ip) {
        if (preg_match("'\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b'", $ip)) {
            return true;
        } else {
            return false;
        }
    }

    function getUserIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        //echo 'IP:'.$ip;
        return $ip;
    }

    function chkUsername() {

        $formfrom = JRequest::getVar('from', '', 'get', 'cmd');
        //var_dump($formfrom);
        $varsessione = 'chkusername';
        // Get the user data.
        //
		/*
          $this->resetTimes($varsessione);
          if (!$this->isOverTimes($varsessione)){
          $response['html'] = '<div><p><b> '.$username.'</b>'.JText::_( 'OVERTIME' ).'</p></div>';
          $response['msg'] = 'false';
          echo (json_encode( $response )) ;
          // Return to keep the application from going anywhere else
          return true;
          }
         */
        if ($username = JRequest::getVar('username', '', 'get', 'cmd')) {

            $db = & JFactory::getDBO();
            // Grab the fields for the selected table
            $query = 'SELECT id  FROM #__users'
                    . ' WHERE username = ' . $db->Quote($username);
            //. ' AND block = 0';
            $db->setQuery($query);
            $result = $db->loadObject();
            if ($result) {

                // If failure, even though we have nothing to respond as HTML, we set response['html'] to an empty string only to avoid JS errors on the client side.
                if ($formfrom == 'register') {
                    $response['html'] = '<div><p><b> ' . $username . '</b> ' . JText::_('COM_AA4J_UNAME_NOT_AVAILABLE') . '</p></div>';
                    $response['msg'] = 'false';
                    //   $this->addTimes($varsessione);
                } else {
                    if ($result->block == 0) {
                        // Grab the fields for the selected table
                        $query = 'SELECT id, hscore FROM #__users_visit'
                                . ' WHERE id = ' . $result->id;
                        //. ' AND block = 0';
                        $db->setQuery($query);
                        $result2 = $db->loadObject();
                        if (($result2) && ($result2->hscore > 4)) {
                            $response['html'] = '<div><p>' . JText::_('Spammer: ') . '<b> ' . $username . '</b></p></div>';
                            $response['msg'] = 'false';
                        } else {
                            $response['html'] = '<div><p>' . JText::_('HI') . '<b> ' . $username . '</b></p></div>';
                            $response['msg'] = 'true';
                        }
                    } else {
                        $response['html'] = '<div><p>' . JText::_('HI') . '<b> ' . $username . '</b>' . JText::_(' your account is pending') . '</p></div>';
                        $response['msg'] = 'false';
                    }
                    //    $this->resetTimes($varsessione);
                }
            } else {
                if ($formfrom == 'register') {
                    // HTML part of our response is the list, and there is a message as well
                    $response['html'] = '<div><p>' . JText::_('COM_AA4J_UNAME_AVAILABLE') . '</p></div>';
                    $response['msg'] = 'true';
                    //  $this->resetTimes($varsessione);
                } else {
                    $response['html'] = '<div><p><b> ' . $username . '</b>' . JText::_('UNKNOW_USERNAME') . '</p></div>';
                    //This username not exist
                    $response['msg'] = 'false';
                    //     $this->addTimes($varsessione);
                }
            }
        } else {
            // If failure, even though we have nothing to respond as HTML, we set response['html'] to an empty string only to avoid JS errors on the client side.
            $response['html'] = '<div><p>' . JText::_('COM_AA4J_INVALID') . '</p></div>';
            //$response['msg'] = JText::_( 'No name entered' );
            $response['msg'] = 'false';
            // $this->addTimes($varsessione);
        }
        echo (json_encode($response));

        // Return to keep the application from going anywhere else
        return true;
    }

///		
    function chkEmail() {

        $formfrom = JRequest::getVar('from', '', 'get', 'cmd');
        //var_dump($formfrom);
        $varsessione = 'chkemail';
        /*
          $this->resetTimes($varsessione);
          if (!$this->isOverTimes($varsessione)){
          $response['html'] = '<div><p><b> '.$username.'</b>'.JText::_( 'OVERTIME' ).'</p></div>';
          $response['msg'] = 'false';
          echo (json_encode( $response )) ;
          // Return to keep the application from going anywhere else
          return true;
          }
         */

        //$requestData = JRequest::getVar('jform', array(), 'post', 'array');
        //var_dump(JRequest::getVar( 'email', '', 'get' ));
        if ($email = JRequest::getVar('email', '', 'get')) {

            $db = & JFactory::getDBO();
            // Grab the fields for the selected table
            $query = 'SELECT id  FROM #__users'
                    . ' WHERE email = ' . $db->Quote($email);
            //. ' AND block = 0';
            //  var_dump($query);
            $db->setQuery($query);
            $result = $db->loadObject();
            if ($result) {

                // If failure, even though we have nothing to respond as HTML, we set response['html'] to an empty string only to avoid JS errors on the client side.
                if ($formfrom == 'register') {
                    $response['html'] = '<div><p><b> ' . $email . '</b> ' . JText::_('COM_AA4J_UMAIL_NOT_AVAILABLE') . '</p></div>';
                    $response['msg'] = 'false';
                    //   $this->addTimes($varsessione);
                } else {
                    if ($result->block == 0) {
                        // Grab the fields for the selected table
                        $query = 'SELECT id, hscore FROM #__users_visit'
                                . ' WHERE id = ' . $result->id;
                        //. ' AND block = 0';
                        $db->setQuery($query);
                        $result2 = $db->loadObject();
                        if (($result2) && ($result2->hscore > 4)) {
                            $response['html'] = '<div><p>' . JText::_('Spammer: ') . '<b> ' . $username . '</b></p></div>';
                            $response['msg'] = 'false';
                        } else {
                            $response['html'] = '<div><p>' . JText::_('HI') . '<b> ' . $username . '</b></p></div>';
                            $response['msg'] = 'true';
                        }
                    } else {
                        $response['html'] = '<div><p>' . JText::_('HI') . '<b> ' . $username . '</b>' . JText::_(' your account is pending') . '</p></div>';
                        $response['msg'] = 'false';
                    }
                    //    $this->resetTimes($varsessione);
                }
            } else {
                if ($formfrom == 'register') {
                    // HTML part of our response is the list, and there is a message as well
                    $response['html'] = '<div><p>' . JText::_('COM_AA4J_UMAIL_AVAILABLE') . '</p></div>';
                    $response['msg'] = 'true';
                    //  $this->resetTimes($varsessione);
                } else {
                    $response['html'] = '<div><p><b> ' . $username . '</b>' . JText::_('COM_AA4J_INVALID') . '</p></div>';
                    //This username not exist
                    $response['msg'] = 'false';
                    //     $this->addTimes($varsessione);
                }
            }
        } else {
            // If failure, even though we have nothing to respond as HTML, we set response['html'] to an empty string only to avoid JS errors on the client side.
            $response['html'] = '<div><p>' . JText::_('No name entered') . '</p></div>';
            //$response['msg'] = JText::_( 'No name entered' );
            $response['msg'] = 'false';
            // $this->addTimes($varsessione);
        }
        echo (json_encode($response));

        // Return to keep the application from going anywhere else
        return true;
    }

    function doxml() {
        $lat = JRequest::getVar('lat', '', 'get', 'cmd');
        $lon = JRequest::getVar('lon', '', 'get', 'cmd');
        $distanza = JRequest::getVar('dis', '', 'get', 'cmd');
        //  jexit($distanza);
        JPluginHelper::importPlugin('alikonweb');
        $dispatcher = & JDispatcher::getInstance();
        $nomefilexml = $dispatcher->trigger('onDistance', array($lat, $lon, $distanza));
        $response = true;
        $nomefilexml = $nomefilexml[0];
        // jexit(var_dump($nomefilexml));
        echo (json_encode($nomefilexml));
        // Return to keep the application from going anywhere else
        return true;
    }

    function refreshGeo() {
        $lat = JRequest::getVar('lat', '', 'get', 'cmd');
        $lon = JRequest::getVar('lon', '', 'get', 'cmd');
        $ind = JRequest::getString('address', '', 'get', 'cmd');
        $stato = JRequest::getString('stato', '', 'get', 'cmd');
        $siglastato = JRequest::getVar('siglastato', '', 'get', 'cmd');
        $citta = JRequest::getString('citta', '', 'get', 'cmd');
        $db = & JFactory::getDBO();
        $user = JFactory::getUser();
        $userid = $user->get('id');
        //       echo('userid'.$userid);
        $query = 'UPDATE #__userextras set latitude =' . $db->quote($lat) .
                ' ,longitude =' . $db->quote($lon) .
                ' ,acamp1=' . $db->quote($ind) .
                ' ,citta=' . $db->quote($citta) .
                ' ,stato=' . $db->quote($siglastato) .
                ' ,countryname=' . $db->quote($stato) .
                ' WHERE id=' . $user->get('id');
        //      echo $query;
        $db->setQuery($query);
        $db->query();
        $ret = true;
        if ($db->getErrorNum()) {
            $ret = false;
            //         jexit('false');
        }
        echo (json_encode($ret));
        // Return to keep the application from going anywhere else
        return true;
    }

}