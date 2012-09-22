<?php

/**
 * @package	Alikonweb Applications 4 Joomla
 * @subpackage	Alikonweb Youtube Plugin
 * @license		GNU General Public License version 3; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgAlikonwebYoutubevideo extends JPlugin {

    protected $is_online = null;

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
        $this->loadLanguage('plg_alikonweb_youtubevideo.sys', JPATH_ADMINISTRATOR);
        $this->is_connected();
    }

    function onVideo($oggetto) {
        //JPlugin::loadLanguage( 'plg_alikonweb_alikonweb.youtubevideo',JPATH_ADMINISTRATOR );  
        if (!$this->is_online) {
            $url = 'Not connected';
            return $url;
        };
        $par = explode('|', $oggetto);
        $par1 = $par[0];  // the source
        $vid = $par[1] . '&autoplay=1'; // the video
        $par3 = $par[2]; // the height
        $par4 = $par[3]; // the width                                  
        /* default parameters no more needed cause passed

          $width     = $params->get('width', '425');
          $height    = $params->get('height', '350');
          $source    = $params->get('source', '0');
         */
        $link_url = JURI::base() . 'plugins/alikonweb/alikonweb_plgAvideo/';
        JHTML::_('behavior.mootools');
        // JHTML::script('videobox.js', $link_url, true);
        // JHTML::script('swfobject.js', $link_url, true);      
        // JHTML::stylesheet('videobox.css', $link_url, null);
        $url = '<!-- inizio alikonweb video plugin for joomla components -->';
        switch ($par1) {
            case '0':
                //localhost
                $url.='<div>';
                $url.='<object width="325" height="250">';
                $url.='<param name="movie" value="' . JURI::base() . 'images/' . $par[1] . '">';
                $url.='<embed src="' . JURI::base() . 'images/' . $par[1] . '" width="325" height="250"></embed>';
                $url.='</object></div>';
                break;
            case '1':
                $vid = substr($vid, 0, 11);

                $url.='<div>';
                $url.='<object width="' . $par3 . '" height="' . $par4 . '">';
                $url.='<param name="movie" value="http://www.youtube.com/v/' . $vid . '">';
                $url.='</param><param name="allowFullScreen" value="true">';
                $url.='</param><param name="allowscriptaccess" value="always">';
                $url.='</param><embed src=http://www.youtube.com/v/' . $vid . ' ';
                $url.='type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' . $par3 . '" height="' . $par4 . '">';
                $url.='</embed></object></div>';
                break;
            case '2':
                $url.='<div><a href="http://video.google.com/videoplay?docid=' . $vid . ' " rel="vidbox ' . $par3 . ' ' . $par4 . '"  title="Google Video example.">' . JText::_('WATCH_VIDEO') . '</a></div>';
                break;
            case '3':
                $url.='<div><a href="http://www.metacafe.com/watch/' . $vid . '" rel="vidbox ' . $par3 . ' ' . $par4 . '"  title="Metacafe Video example.">' . JText::_('WATCH_VIDEO') . '</a></div>';
                break;
        }
        $url.='<!-- fine alikonweb video plugin for joomla components -->';
        return $url;
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

}
