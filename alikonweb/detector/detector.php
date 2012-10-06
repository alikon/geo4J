<?php
/** 06/10/2012 11.54
 * @package	Alikonweb Applications 4 Joomla
 * @subpackage	Alikonweb Detector Plugin
 * @license		GNU General Public License version 3; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgAlikonwebDetector extends JPlugin {

    protected $ip = null;
    protected $email = null;
    protected $username = null;
    protected $text = null;
    protected $webite = null;
    protected $is_online = null;
    protected $info_localize = array();
    protected $info_spam = array();

    public function __construct(&$subject, $config) {
        $this->ip = null;
        $this->is_online = null;
        $this->info_localize = array('status' => 'ko', 'latitude' => '0', 'longitude' => '0', 'zippostalcode' => '', 'city' => 'unknown', 'region_name' => '', 'country_name' => 'Unknown', 'country_code' => 'UN', 'ip' => $this->ip);
        $this->info_spam = array('status' => 'ko', 'text' => 'Off-line', 'score' => 0);
        parent::__construct($subject, $config);

        $this->loadLanguage('plg_alikonweb_detector.sys', JPATH_ADMINISTRATOR);
        $this->is_connected();
        
    }

    public function onDetectFull($userip, $usermail, $username, $text, $website) {
        if ($this->is_online) {
            $info_full = array();
            $info_full = $this->onDetectUser($userip, $usermail, $username, $text, $website);
            if ($this->info_full['score'] < 5) {
                $info_full = $this->onDetectText($userip, $usermail, $username, $text, $website);
            }
            return $info_full;
        } else {
            return array_merge($this->info_spam, $this->info_localize);
        }
        
    }

    public function onDetectText($userip, $usermail, $username, $text, $website) {
        if ($this->is_online) {
            $this->username = $username;
            $this->email = $usermail;
            $this->text = $text;
            $this->website = $website;
            $this->info_spam = $this->plgAkismet();
            if ($this->info_spam['score'] < 5) {
                $this->info_spam = $this->plgDefensio();
            }
        }
        return array_merge($this->info_spam, $this->info_localize);
    }

    public function onDetectUser($userip, $usermail, $username, $text, $website) {
        $this->ip = $userip;
        $this->email = $usermail;
        $this->username = $username;
        $this->info_localize['ip'] = $this->ip;
        if ($this->is_online) {
            if ($this->ip == null) {
                $this->ip = $this->getIpAddr();
            }


            $this->info_spam = $this->plgHoneypot2ip();

            if ($this->info_spam['score'] < 5) {
                $this->info_spam = $this->plgStopforumspam2ip();
            }

            if ($this->info_spam['score'] < 5) {
                $this->info_spam = $this->plgBotscout2ip();
            }

            if ($this->info_spam['score'] < 5) {
                $this->info_spam = $this->plgSorbs();
            }


            if ($this->info_spam['score'] < 5) {
                $this->info_spam = $this->plgFspamlist();
            }


            if ($this->info_spam['score'] < 5) {
                $this->info_spam = $this->plgSpamhaus();
            }

            if ($this->info_spam['score'] < 5) {
                $this->info_spam = $this->plgSpamcop();
            }

            $this->info_localize = $this->plgLocalizeIp();
        }
        return array_merge($this->info_spam, $this->info_localize);
    }

    private function getIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
            $this->ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }
        // echo 'IP:'.$this->ip;
        return $this->ip;
    }

    private function is_connected() {
        //check to see if the local machine is connected to the web 
        //uses sockets to open a connection to google.com 
        $connected = @fsockopen("www.google.com", 80);
        if ($connected) {
            $this->is_online = true;
            fclose($connected);
        } else {
            $this->is_online = false;
        }
        return true;
    }

    private function plgStopforumspam2ip() {

        $status = 'ok';

        $stopforumspam_api_key = $this->params->get('stopforumspam_api_key', '');
        $stopforumspam_use = $this->params->get('stopforumspam_use', 0);
        $response = '';

        if (!$stopforumspam_use) {
            return array('status' => 'ko', 'text' => 'StopForumSpam disabled', 'score' => 0);
        }
        // new mic: check for keys to avoid unnesserary loads!
        // do not submit data to stopforumspam so api key is unnecessary at this moment
        //if (!$stopforumspam_api_key) {
        //    return array('status' => 'ko', 'text' => 'StopForumSpam no api key', 'score' => 0);
        //}
        //JLoader::register('Akismet', dirname(__FILE__) . '/Akismet.class.php');
        //require_once(dirname(__FILE__) . DS . 'alikonweb_plgDetector' . DS . 'Stopforumspam.function.php');
        JLoader::register('StopForumSpam', dirname(__FILE__) . DS . 'alikonweb_plgDetector' . DS . 'Stopforumspam.class.php');
        $stopforumspam = new StopForumSpam($this->email, $this->ip);
        $spambot_report = $stopforumspam->checkSpambots();
        return $spambot_report;
    }

    private function plgLocalizeIp() {

        $ipinfodb_api_use = $this->params->get('ipinfodb_api_use', 0);
        $ipinfodb_api_key = $this->params->get('ipinfodb_api_key', '');
        $response = array('status' => 'ko', 'latitude' => '0', 'longitude' => '0', 'zippostalcode' => '', 'city' => 'unknown', 'region_name' => '', 'country_name' => 'Unknown', 'country_code' => 'UN', 'ip' => $this->ip);
        if ($this->ip=='127.0.0.1'){
            return $response;
        }   
        if (($ipinfodb_api_use) && ($ipinfodb_api_key != '')) {
            JLoader::register('IpInfoDb', dirname(__FILE__) . DS . 'alikonweb_plgDetector' . DS . 'infoDB.class.php');
            $ipinfodb = new IpInfoDb($ipinfodb_api_key, $this->ip);
            $response = $ipinfodb->ipLocation();
        }
        return $response;
    }

    private function plgHoneypot2ip() {

        $status = 'ok';

        $honey_api_key = $this->params->get('honey_api_key', '');
        $honey_api_use = $this->params->get('honey_api_use', 0);
        $response = '';

        // new mic: check for keys to avoid unnesserary loads!
        if (!$honey_api_key) {
            return array('status' => 'ko', 'text' => 'HoneyPot no api key', 'score' => 0);
        }
        if (!$honey_api_use) {
            return array('status' => 'ko', 'text' => 'HoneyPot disabled', 'score' => 0);
        }

        JLoader::register('http_bl', dirname(__FILE__) . DS . 'alikonweb_plgDetector' . DS . 'Honeypot.class.php');

        $h = new http_bl($honey_api_key);

        $r = $h->query($this->ip);

        if ($r == 2) {
            $honey_report = JText::sprintf('HoneyPot Found a %s (%s) with a score of %s. last seen since %s days', $h->type_txt, $h->type_num, $h->score, $h->days);
        } elseif ($r == 1) {
            $honey_report = JText::sprintf('HoneyPot Found a Search Engine (%s)', $h->engine_num);
        } else {
            $honey_report = JText::_('HoneyPot Not found');
        }

        $response = array('status' => $status, 'text' => $honey_report, 'score' => $h->type_num);

        return $response;
    }

    private function plgBotscout2ip() {

        $scout_api_use = $this->params->get('scout_api_use', 0);
        $scout_api_key = $this->params->get('scout_api_key', '');
        $response = '';
        //jexit('JINVALIDuse:'.$scout_api_use);
        if (!$scout_api_use) {
            return array('status' => 'ko', 'text' => 'BotScout disabled', 'score' => 0);
        }

        // new mic: check for keys to avoid unnesserary loads!
        if (!$scout_api_key) {
            return array('status' => 'ko', 'text' => 'BotScout no api key', 'score' => 0);
        }
        JLoader::register('Botscout', dirname(__FILE__) . DS . 'alikonweb_plgDetector' . DS . 'Botscout.class.php');
        $botscout = new Botscout($this->email, $this->ip, $ipinfodb_api_key);
        $response = $botscout->http_botscout();
        return $response;
    }

    private function plgSorbs() {


        $sorbs_use = $this->params->get('sorbs_use', 0);
        if (!$sorbs_use) {
            return array('status' => 'ko', 'text' => JText::_('Sorbs disabled'), 'score' => 0);
        }

        $address = $this->ip;
        $rev = implode('.', array_reverse(explode('.', $address)));

        //
        // Check the IP against Sorbs
        //			
        $sorbsresult = false;
        $lookup = $rev . '.l1.spews.dnsbl.sorbs.net.';
        // The response code from the SpamCop server to indicate a queried IP is listed is 127.0.0.2

        $lookupResult = gethostbyname($lookup);
        if ($lookup != $lookupResult) {

            $sSHDB = 'Sorbs (RawResponse=' . $lookupResult . ')';
            $sorbsresult = true;
        } // End switch
        if ($sorbsresult == true) {
            $response = array('status' => 'ok', 'text' => JText::_('Sorbs found ') . $sSHDB, 'score' => 157);
        } else {
            $response = array('status' => 'ok', 'text' => JText::_('Sorbs not found'), 'score' => 0);
        } // End if


        return $response;
    }

    private function plgSpamhaus() {


        $spamhaus_use = $this->params->get('spamhaus_use', 0);
        if (!$spamhaus_use) {
            return array('status' => 'ko', 'text' => JText::_('Spamhaus disabled'), 'score' => 0);
        }

        $address = $this->ip;
        $rev = implode('.', array_reverse(explode('.', $address)));

        //
        // Check the IP against Spamhaus
        //			
        $spamhausspambot = false;
        $lookup = $rev . '.zen.spamhaus.org.';
        // Spamhaus returns codes based on which blacklist the IP is in;
        //
			// 127.0.0.2		= SBL (Direct UBE sources, verified spam services and ROKSO spammers)
        // 127.0.0.3		= Not used
        // 127.0.0.4-8		= XBL (Illegal 3rd party exploits, including proxies, worms and trojan exploits)
        //	- 4		= CBL
        //	- 5		= NJABL Proxies (customized)
        // 127.0.0.9		= Not used
        // 127.0.0.10-11	= PBL (IP ranges which should not be delivering unauthenticated SMTP email)
        //	- 10		= ISP Maintained
        //	- 11		= Spamhaus Maintained
        //
			// We don't flag the CBL or PBL here.
        $spamhaustemp = gethostbyname($lookup);
        switch ($spamhaustemp) {
            case "127.0.0.2":
                $sSHDB = "(SBL) ";
                $spamhausspambot = true;
                break;
            case "127.0.0.4": // We don't flag those in the CBL
                $sSHDB = "(CBL) ";
                $spamhausspambot = false;
                break;
            case "127.0.0.5":
                $sSHDB = "(NJABL) ";
                $spamhausspambot = true;
                break;
            case "127.0.0.6":
                $sSHDB = "(XBL) ";
                $spamhausspambot = true;
                break;
            case "127.0.0.7":
                $sSHDB = "(XBL) ";
                $spamhausspambot = true;
                break;
            case "127.0.0.8":
                $sSHDB = "(XBL) ";
                $spamhausspambot = true;
                break;
            case "127.0.0.10": // We don't flag those in the PBL
                $sSHDB = "(PBL - ISP Maintained) ";
                $spamhausspambot = false;
                break;
            case "127.0.0.11": // We don't flag those in the PBL
                $sSHDB = "(PBL - Spamhaus Maintained) ";
                $spamhausspambot = false;
                break;
            default: // We only flag valid responses
                $sSHDB = "";
                $spamhausspambot = false;
                break;
        } // End switch
        if ($spamhausspambot == true) {
            $response = array('status' => 'ok', 'text' => JText::_('Spamhaus found ') . $sSHDB, 'score' => 14);
        } else {
            $response = array('status' => 'ok', 'text' => JText::_('Spamhaus not found'), 'score' => 0);
        } // End if

        return $response;
    }

    private function plgSpamcop() {

        $spamhaus_use = $this->params->get('spamcop_use', 0);
        if (!$spamhaus_use) {
            return array('status' => 'ko', 'text' => JText::_('SpamCop disabled'), 'score' => 0);
        }

        $address = $this->ip;
        $rev = implode('.', array_reverse(explode('.', $address)));

        //
        // Check the IP against Spamhaus
        //			
        $spamcopresult = false;
        $lookup = $rev . '.bl.spamcop.net.';
        // The response code from the SpamCop server to indicate a queried IP is listed is 127.0.0.2

        $lookupResult = gethostbyname($lookup);
        if ($lookupResult == '127.0.0.2') {

            $sSHDB = 'SpamCop (RawResponse=' . $lookupResult . ')';
            $spamcopresult = true;
        } // End switch
        if ($spamcopresult == true) {
            $response = array('status' => 'ok', 'text' => JText::_('SpamCop found ') . $sSHDB, 'score' => 147);
        } else {
            $response = array('status' => 'ok', 'text' => JText::_('SpamCop not found'), 'score' => 0);
        } // End if

        return $response;
    }

    private function plgFspamlist() {

        $fspamlist_use = $this->params->get('fspamlist_use', 0);
        $fspamlist_api_key = $this->params->get('fspamlist_api_key', '');
        if (!$fspamlist_use) {
            return array('status' => 'ko', 'text' => JText::_('Fspamlist disabled'), 'score' => 0);
        }
        if ($fspamlist_api_key == '') {
            return array('status' => 'ko', 'text' => JText::_('FspamList No API Key'), 'score' => 0);
        }
        $xml_string = file_get_contents('http://www.fspamlist.com/api.php?key=' . $fspamlist_api_key . '&spammer=' . $this->email . ',' . $this->username . ',' . $this->ip);
        if ($xml_string === false) {
            return array('status' => 'ko', 'text' => JText::_('FspamList no connection'), 'score' => 0);
        }
        //jexit('Fspamllist:'.$xml_string);
        if (($xml_string == 'Invalid API Key')||($xml_string == 'Invalid query.')) {

            return array('status' => 'ko', 'text' => JText::_('FspamList Invalid API Key'), 'score' => 0);
        } else {
            $response = array('status' => 'ok', 'text' => JText::_('FspamList no spam'), 'score' => 0);
            $xml = new SimpleXMLElement($xml_string);
            foreach ($xml->children() as $node) {
                $arr = $node->attributes();   // returns an array
                if ($node->isspammer == 'true') {
                    if ($node->spammer == $this->ip) {
                        //return array('status' => 'ok','text' => JText::_( 'FspamList In database for ip ' ).$node->timesreported.JText::_( ' times' ),'score' => 10);
                        // echo '10'; return;
                        $response = array('status' => 'ok', 'text' => JText::_('FspamList In database for ip ') . $node->timesreported . JText::_(' times'), 'score' => 10);
                        break;
                    } else {
                        // return array('status' => 'ok','text' => JText::_( 'FspamList In database for email ' ).$node->timesreported.JText::_( ' times' ),'score' => 11);
                        $response = array('status' => 'ok', 'text' => JText::_('FspamList In database for email ') . $node->timesreported . JText::_(' times'), 'score' => 11);
                        break;
                        //	echo '11'; return;
                    }
                }
            }
            return $response;
        }
    }

    private function plgAkismet() {

        $akismet_api_key = $this->params->get('akismet_api_key', '');
        $akismet_use = $this->params->get('akismet_use', 0);
        if ($akismet_use == 0) {
            return array('status' => 'ko', 'text' => 'Akismet disabled', 'score' => 0);
        }
        if (!$akismet_api_key == '') {
            $comment = array(
                'author' => $this->username,
                'email' => $this->email,
                'website' => $this->website,
                'body' => $this->text,
                'permalink' => JURI::base()
            );

            //$akismet = new Akismet('http://www.yourdomain.com/', 'YOUR_WORDPRESS_API_KEY', $comment);
            //JLoader::register('Akismet', dirname(__FILE__) . DS . 'alikonweb_plgDetector' . DS . 'Akismet.class.php');
            require_once(dirname(__FILE__) . DS . 'alikonweb_plgDetector' . DS . 'Akismet.class.php');
            $akismet = new Akismet(JURI::base(), $akismet_api_key, $comment);
            if ($akismet->errorsExist()) {
                $response = array('status' => 'ko', 'text' => 'Not connected to Akismet server!', 'score' => 0);
            } else {
                if ($akismet->isSpam()) {
                    $response = array('status' => 'ko', 'text' => 'Akismet:Spam detected', 'score' => 12);
                } else {
                    $response = array('status' => 'ko', 'text' => 'Akismet:No spam!', 'score' => 0);
                }
            }
        } else {
            $response = array('status' => 'ko', 'text' => 'Akismet no api key', 'score' => 0);
        }
        return $response;
    }

    private function plgDefensio() {

        $response = 'No Defensio Api key';
        $document = array();

        $defensio_api_key = $this->params->get('defensio_api_key', '');
        $defensio_use = $this->params->get('defensio_use', 0);
        if ($defensio_use == 0) {
            return array('status' => 'ko', 'text' => 'Defensio disabled', 'score' => 0);
        }
        If (!$defensio_api_key == '') {
            //require_once(dirname(__FILE__) . DS . 'alikonweb_plgDetector' . DS . 'Defensio.php');
            JLoader::register('Defensio', dirname(__FILE__) . DS . 'alikonweb_plgDetector' . DS . 'Defensio.php');
            $defensio = new Defensio($defensio_api_key);
            if (array_shift($defensio->getUser()) == 200) {
                $document = array(
                    'type' => 'comment',
                    'content' => $this->text,
                    'platform' => 'alikonweb_joomla',
                    'client' => 'Defensio-PHP Example | 0.1 | alikon | info@alikonweb.it',
                    'async' => 'false'
                );
                $post_result = $defensio->postDocument($document);
                $doc1_signature = $post_result[1]->signature;
                $get_result = $defensio->getDocument($doc1_signature);
                if ($get_result[1]->status == 'success') {
                    switch ($get_result[1]->classification) {
                        case 'legitimate':
                            $response = array('status' => 'ko', 'text' => 'Defensio: legitimate', 'score' => 0);
                            break;
                        case 'innocent':
                            $response = array('status' => 'ko', 'text' => 'Defensio: innocent', 'score' => 0);
                            break;
                        case 'malicious':
                            $response = array('status' => 'ko', 'text' => 'Defensio: malicious', 'score' => 13);
                            break;
                        case 'spam':
                            $response = array('status' => 'ko', 'text' => 'Defensio: spam', 'score' => 14);
                            break;
                    }
                }
            } else {
                $response = array('status' => 'ko', 'text' => 'Defensio:Api key is invalid', 'score' => 0);
            }
        } else {
            $response = array('status' => 'ko', 'text' => 'Defensio: no api key', 'score' => 0);
        }


        return $response;
    }

}
