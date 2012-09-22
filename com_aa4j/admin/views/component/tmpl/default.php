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
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$template = JFactory::getApplication()->getTemplate();

JPluginHelper::importPlugin('alikonweb');
$dispatcher = & JDispatcher::getInstance();

if ($this->option == 'geo') {
    $results = $dispatcher->trigger('onXml', array($mode, 'usermap.xml', 'citta', '1', '880', '400', 42, 12));
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_aa4j&view=component'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-select fltrt">
            <label for="filter_state">
                <?php echo JText::_('COM_AA4J_FILTER_LABEL'); ?>
            </label>


            <select name="filter_nation" id="filter_nation" class="inputbox" onchange="this.form.submit()">
                <option value="*"><?php echo JText::_('COM_AA4J_OPTION_FILTER_NATION'); ?></option>
                <?php echo JHtml::_('select.options', Componenthelper::getNationOptions(), 'value', 'text', $this->state->get('filter.nation')); ?>
            </select>
        </div>
    </fieldset>
    <div class="clr"> </div>


    <?php
    $contents = '<div style="padding:1px;">' . $results[0] . '</div>';
    echo $contents;
//  echo 'option:'.$this->option;
