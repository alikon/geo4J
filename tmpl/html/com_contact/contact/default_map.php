<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

if ('plain' == $this->params->get('presentation_style')) :
    echo '<h3>' . JText::_('COM_CONTACT_MAP') . '</h3>';
else :
    echo JHtml::_($this->params->get('presentation_style') . '.panel', JText::_('COM_CONTACT_MAP'), 'display-map');
endif;
?>

<div class="contact-map">
    <?php
    $contents = '';
    $indirizzo = '';

    if (JPluginHelper::isEnabled('alikonweb', 'googlemap')) {

        $zoom = (int) $this->params->get('zoom');

        if (($zoom < 1) || ($zoom > 19)) {
            $zoom = 9;
        }
        switch ($this->params->get('type_googlemaps')) {
            case '0':
                $mode = 'address';
                break;
            case '1':
                $mode = 'percorso';
                break;
            case '2':
                $mode = 'street';
                break;
        }
        //var_dump($coord);
        //$mode='address';
        $coord = array(0, 0);
        $lat = 0;
        $lon = 0;
        $width = $this->params->get('mwidth', 200);
        $eight = $this->params->get('mheight', 300);
        $base = $this->params->get('base', 'rome');
        $dest = $this->params->get('dest', 'bari');
        $text = $this->params->get('text', 'no text for ip:');
        $gmap_type = $this->params->get('type_googlemaps');
        //$text ='<b>Name:</b> '.$this->contact->name.'<br /><b>Position:</b> '.$this->contact->con_position.'<br /><b>Address:</b> '.$this->contact->params->get( 'gmap_ind' );
        //if($indirizzo==''){
        $indirizzo = $base;
        //$text ='no text for ip '.$ip;
        //}
        JPluginHelper::importPlugin('alikonweb');
        $dispatcher = & JDispatcher::getInstance();

        switch ($gmap_type) {
            case 0:
                $results = $dispatcher->trigger('onShowMap', array($mode, $text, $indirizzo, $zoom, $width, $eight, $lat, $lon));
                break;
            case 1:
                $results = $dispatcher->trigger('onShowDirections', array($mode, $text, $indirizzo, $zoom, $width, $eight, $lat, $lon, $dest));
                break;

            case 2:

                $coord = $dispatcher->trigger('onNation', array($this->params->get('stato', 'IT')));
                $naxml = $this->params->get('stato', 'IT') . 'usermap.xml';
                //  echo 'nation:'.$naxml.' coord:'.$coord[0][0].','.$coord[0][1];
                $results = $dispatcher->trigger('onXml', array($mode, $naxml, $indirizzo, $zoom, $width, $eight, $coord[0][0], $coord[0][1]));
                break;
            case 3:

                $info_detector = $dispatcher->trigger('onDetectUser', array(null, 'pippo@pippo.it', 'nome', ' ', ' '));
                $stato = $info_detector[0]['country_code'];
                $coord = $dispatcher->trigger('onNation', array($stato));
                $naxml = $stato . 'usermap.xml';
                //   echo 'nation:'.$stato.' coord:'.$coord[0][0].','.$coord[0][1];
                $results = $dispatcher->trigger('onXml', array($mode, $naxml, $indirizzo, $zoom, $width, $eight, $coord[0][0], $coord[0][1]));
                break;
        }


        $contents.='<div style="padding:1px;">' . $results[0] . '</div>';
        echo $contents;
    } else {
        echo 'plugin not loaded';
    }
    ?>       
</div>  
