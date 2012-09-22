<?php

defined('_JEXEC') or die;

class StopForumSpam {

    private $mail = null;
    private $ip = null;

    /**
     * 	@param	string	$email	The mail.
     * 	@param	string	$ip	the ip.
     */
    public function __construct($email, $ip) {
        $this->mail = $email;
        $this->ip = $ip;

        // Set some default values
    }

    public function checkSpambots() {

        $spambot = array('status' => 'ok', 'text' => JText::_('Not found'), 'score' => 0);

        //check the e-mail adress
        $xml_string = @file_get_contents('http://www.stopforumspam.com/api?email=' . $this->mail);
        if ($xml_string != '') {
            $xml = @new SimpleXMLElement($xml_string);
            if ($xml->appears == 'yes') {
                $spambot = array('status' => 'ok', 'text' => JText::_('Stopforumspam: email found in database ') . $xml->frequency . JText::_(' times'), 'score' => 9);
                return $spambot;
            }
        }
        //e-mail not found in the database, now check the ip
        $xml_string = @file_get_contents('http://www.stopforumspam.com/api?ip=' . $this->ip);

        if ($xml_string != '') {
            $xml = @new SimpleXMLElement($xml_string);
            if ($xml->appears == 'yes') {
                $spambot = array('status' => 'ok', 'text' => JText::_('Stopforumspam: ip found in database ') . $xml->frequency . JText::_(' times'), 'score' => 9);
            }
        }

        return $spambot;
    }

}