<?php
/**
 * @version		$Id: view.html.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');
jimport('joomla.html.pane');
/**
 * @package		Joomla.Administrator
 * @subpackage	com_config
 */
class Aa4jViewApplication extends JView
{
	public $state;
	public $form;
	public $data;

	/**
	 * Method to display the view.
	 */
	public function display($tpl = null)
	{
		$form	= $this->get('Form');
		$data	= $this->get('Data');

		// Check for model errors.
		if ($errors = $this->get('Errors')) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Bind the form to the data.
		if ($form && $data) {
			$form->bind($data);
		}
    $pane 	=& JPane::getInstance('sliders');

		$this->assignRef('pane', $pane);
		// Get the params for com_users.
		$usersParams = JComponentHelper::getParams('com_users');

		// Get the params for com_media.
		$mediaParams = JComponentHelper::getParams('com_media');

		// Load settings for the FTP layer.
		jimport('joomla.client.helper');
		$ftp = JClientHelper::setCredentialsFromRequest('ftp');

		$this->assignRef('form',	$form);
		$this->assignRef('data',	$data);
		$this->assignRef('ftp',		$ftp);
		$this->assignRef('usersParams', $usersParams);
		$this->assignRef('mediaParams', $mediaParams);
		/*
	$toolbar =& JToolBar::getInstance('toolbar');
//	$url = JRoute::_('index.php?option=com_aa4j&task=detecttask&format=raw');
			//$toolbar->prependButton('Popup', 'help', 'Info', 'index.php?option=com_aa4j&view=component&component=42',false);
			$toolbar->prependButton('Popup', 'help', 'Info', 'index.php?option=com_aa4j&view=users',false);
			*/
		$this->addToolbar();
		parent::display($tpl);
	}
	function _quickiconButton( $link, $image, $text, $path=null, $target='', $onclick='' ) {
		$app = JFactory::getApplication('administrator');
		if( $target != '' ) {
	 		$target = 'target="' .$target. '"';
	 	}
	 	if( $onclick != '' ) {
	 		$onclick = 'onclick="' .$onclick. '"';
	 	}
	 	if( $path === null || $path === '' ) {
			$template = $app->getTemplate();
	 		$path = '/templates/'. $template .'/images/header/';
	 	}

	 	$lang = & JFactory::getLanguage();
		?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>" <?php echo $target;?>  <?php echo $onclick;?>>
					<?php echo JHTML::_('image.administrator', $image, $path, NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php 
	}
	function _getTemplate()	{
	 $templates='';
	 $db = JFactory::getDbo();			
	 $query	= $db->getQuery(true);
	 $query->select('template');			
	 $query->from('#__template_styles');
	 $query->where('client_id=0 AND home = 1');
	 $db->setQuery($query);		
	 $templates = $db->loadResult();
	 return $templates;
	}	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('Alikonweb Applications 4 JOOMLA'), 'app');
	//	JToolBarHelper::apply('application.apply', 'JTOOLBAR_APPLY');
	//	JToolBarHelper::save('application.save', 'JTOOLBAR_SAVE');
	//	JToolBarHelper::divider();
	//	JToolBarHelper::cancel('application.cancel', 'JTOOLBAR_CANCEL');
	//	JToolBarHelper::divider();
		JToolBarHelper::preferences('com_aa4j');
		JToolBarHelper::help('JHELP_SITE_GLOBAL_CONFIGURATION');
	}
}