<?php
//no direct accees
defined('_JEXEC') or die('resticted aceess');

jimport('joomla.event.plugin');
jimport('joomla.filesystem.folder');

class plgSystemAContact extends JPlugin {

    function __construct(& $subject, $config) {
        parent::__construct($subject, $config);
        $this->lang = JFactory :: getLanguage();
        $this->lang->load('plg_system_acontact', JPATH_ADMINISTRATOR);
    }

    function onContentPrepareForm($form, $data) {
    	//echo $form->getName();
        $app = JFactory::getApplication();

        if (($form->getName() == 'com_contact.contact') && ($app->isAdmin())) {//Add Acontact menu params to the menu item
            JHtml::_('behavior.framework', true);
            $doc = JFactory::getDocument();
            JForm::addFormPath(JPATH_PLUGINS . DS . 'system' . DS . 'acontact' . DS . 'elements');
            $form->loadFile('params', false);
        }
      
       if (($form->getName() == 'com_content.article') && ($app->isAdmin())) {
            JHtml::_('behavior.framework', true);
            $doc = JFactory::getDocument();
            //JForm::addFormPath(JPATH_PLUGINS . DS . 'system' . DS . 'acontact' . DS . 'elements');
            //$form->loadFile('content');
            $form->loadFile(dirname(__FILE__).'/elements/content.xml');
        }        
     
    }

}

?>