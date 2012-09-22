<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if ('plain' == $this->params->get('presentation_style')) :
	echo '<h3>'.JText::_('COM_CONTACT_VIDEO').'</h3>';
else :
    echo JHtml::_($this->params->get('presentation_style').'.panel', JText::_('COM_CONTACT_VIDEO'), 'display-VIDEO');
endif;
?>

<div class="contact-video">
				<?php 
				   $contents = '';
	//if (JPluginHelper::isEnabled('alikonweb', 'youtubevideo'))  {
		
		$video_source='1';
		$video_id=$this->params->get('videoid');
		$video_width=$this->params->get('width');
		$video_height=$this->params->get('height');
		$para=$video_source.'|'.$video_id.'|'.$video_width.'|'.$video_height;
		JPluginHelper::importPlugin( 'alikonweb', 'youtubevideo' );
		$dispatcher =& JDispatcher::getInstance();			 
		$results = $dispatcher->trigger( 'onVideo',array($para)  );
               // jexit(var_dump($results));
		$contents.='<div style="float:left; padding:1px;">'.$results[0].'</div>';
	  echo $contents;
    
  //}
          
				?>       
</div>
