<?php

/**
 * @version		$Id: default.php 21734 2011-07-04 21:30:32Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;
JHTML::_('behavior.mootools');
JPluginHelper::importPlugin('alikonweb');
    $dispatcher = & JDispatcher::getInstance();
    $results = $dispatcher->trigger('onShowDistance', array(null, 800, 400));
    $contents.='<div style="padding:1px;">' . $results[0] . '</div>';
    echo $contents;
    
