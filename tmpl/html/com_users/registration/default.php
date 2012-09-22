<?php
/**
 * @version		$Id: default.php 
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Alikonweb.it, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$script = <<<EOL
window.addEvent("domready",function(){
var box = $('namechkregister');
var cun = document.id('jform_username');
$(cun).addEvent("blur",function(){   
    if ( $(cun).value.length > 0 ){
        var url="?option=com_aa4j&amp;format=raw&amp;task=chkUsername&amp;from=register&amp;username="+$(cun).value;
        box.style.display="block";
        box.set('html','Check in progress...');
        var a=new Request.JSON({
            url:url,
            onComplete: function(response){  
            	  box.set('html',response.html);         
                 if (response.msg==='false'){
                 $(cun).value='';
                 $(cun).focus();                
                }            
                 var el = $(box);
                 (function(){                
                   el.set('html','');
                 }).delay(1500);                       
                      
                                               
            }
        });
        a.get();
      }
    });
var box2 = $('emailchkregister');
var cue = document.id('jform_email1');    
$(cue).addEvent("blur",function(){  	  	 
    var reg1 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/; // not valid
    var reg2 = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/; // valid
    if (!reg1.test($(cue).value) && reg2.test($(cue).value)) { // if syntax is valid
        var url="?option=com_aa4j&amp;format=raw&amp;task=chkEmail&amp;from=register&amp;email="+$(cue).value;
        box2.style.display="block";
        box2.set('html','Check in progress...');
        var b=new Request.JSON({
            url:url,
            onComplete: function(response){  
            	  box2.set('html',response.html);         
                 if (response.msg==='false'){
                 $(cue).value='';
                 $(cue).focus();                
                }            
                 var el2 = $(box2);
                 (function(){                
                   el2.set('html','');
                 }).delay(1500);                       
                      
                                               
            }
        });
        b.get();
      }
    });    
    
});    
EOL;
$document = JFactory::getDocument();
$document->addScriptDeclaration($script);
?>
<div class="registration<?php echo $this->pageclass_sfx ?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>

    <form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate">
        <?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one. ?>
            <?php $fields = $this->form->getFieldset($fieldset->name); ?>
            <?php if (count($fields)): ?>
                <fieldset>
                    <?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend. ?>
                        <legend><?php echo JText::_($fieldset->label); ?></legend>
                    <?php endif; ?>
                    <dl>
                        <?php foreach ($fields as $field):// Iterate through the fields in the set and display them. ?>
                            <?php if ($field->hidden):// If the field is hidden, just display the input.?>
                                <?php echo $field->input; ?>
                            <?php else: ?>
                                <dt>
                                <?php echo $field->label; ?>
                                <?php if (!$field->required && $field->type != 'Spacer'): ?>
                                    <span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
                                <?php endif; ?>
                                </dt>
                                <dd><?php
                                echo $field->input;
                                if ($field->name == 'jform[username]') {
                                    echo '<span id="namechkregister"></span>';
                                }
                                if ($field->name == 'jform[email1]') {
                                    echo '<span id="emailchkregister"></span>';
                                }
                                ?>
                                </dd>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </dl>
                </fieldset>
            <?php endif; ?>
        <?php endforeach; ?>
        <div>
            <button type="submit" class="validate"><?php echo JText::_('JREGISTER'); ?></button>
            <?php echo JText::_('COM_USERS_OR'); ?>
            <a href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="registration.register" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>
</div>
