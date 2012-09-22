<?php
/**
 * models/cpanel/tmpl/default.php
 *
 * @package		AA4J
 * @subpackage	com_aa4j
 * @version		1.8.1
 * @since		
 *
 * @author		Alikon <info@alikonweb.it>
 * @link		http://www.alikonweb.it
 * @copyright	Copyright (C) 2011 Alikonweb. All Rights Reserved
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL v3
 * This is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
// no direct access
defined('_JEXEC') or die('Restricted access.');
JHTML::_('behavior.tooltip');
jimport('joomla.language.language');
JHtml::_('stylesheet', 'com_aa4j/admin.css', array(), true);
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'version.php');
?>
<table width='100%'>
    <tr>
        <td width='55%' class='adminform' valign='top'>
            <div id='cpanel'>
<?php
//echo JText::_('Alikonweb Application Control Cockpit');
$link = 'index.php?option=com_plugins';
$this->_quickiconButton($link, 'icon-48-plugin.png', JText::_('Plugin Manager'));
$link = 'index.php?option=com_aa4j&task=copyOverride';
$this->_quickiconButton($link, 'icon-48-newcategory.png', JText::_('Template Layout Override'));
$link = 'index.php?option=com_aa4j&view=component&component=geo';
$this->_quickiconButton($link, 'icon-48-language.png', JText::_('GeoLocalization'));
$link = 'index.php?option=com_aa4j&view=users';
$this->_quickiconButton($link, 'icon-48-user.png', JText::_('Members'));
$link = 'index.php?option=com_aa4j&view=component&component=all';
$this->_quickiconButton($link, 'icon-48-groups.png', JText::_('Member Locator'));
                ?>
            </div>
            <div class='clr'></div>
        </td>
        <td valign='top' width='45%' style='padding: 7px 0 0 5px'>
            <?php
            echo $this->pane->startPane('pane');

//$title = JText::_('Welcome_to_AA4J');
            echo $this->pane->startPanel('AA4J', 'welcome');
            ?>
            <table class='adminlist'>
                <tr>
                    <td colspan='2'>
                        <p><?php echo JText::_('Alikonweb Applications for Joomla!') ?></p>
                    </td>
                    <td rowspan='4' style="text-align:center">
                        <a href='http://www.alikonweb.it/'>
                            <img src='components/com_aa4j/alikonlogo_16.png'  alt='Alikonweb' title='Alikonweb 4 Joomla!' align='middle' border='0'>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width='25%'>
                        <?php echo JText::_('Version'); ?>
                    </td>
                    <td width='45%'>
                        <?php echo aa4jVersion::getLongVersion(); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo JText::_('Copyright'); ?>
                    </td>
                    <td>
                        <a href='http://www.alikonweb.it' target='_blank'>&copy; 2005-2012 Alikonweb </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo JText::_('License'); ?>
                    </td>
                    <td>
                        <a href='http://www.gnu.org/licenses/gpl-3.0.html' target='_blank'>GNU GPL v3</a>
                    </td>
                </tr>
            </table>
            <?php
            echo $this->pane->endPanel();

            $title = JText::_('Layout override');
            echo $this->pane->startPanel($title, 'status');
            ?>
            <table class='adminlist'>
                <th>Template</th>
                <th>Article</th>
                <th>Weblink</th>
                <th>Contact</th>
                <th>Login</th>
                <tr>
                    <td>
                        <?php echo $this->_getTemplate(); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'templates' . DS . $this->_getTemplate() . DS . 'html' . DS . 'com_content' . DS . 'form' . DS . 'edit.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'templates' . DS . $this->_getTemplate() . DS . 'html' . DS . 'com_weblinks' . DS . 'form' . DS . 'edit.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'templates' . DS . $this->_getTemplate() . DS . 'html' . DS . 'com_contact' . DS . 'contact' . DS . 'default_form.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'templates' . DS . $this->_getTemplate() . DS . 'html' . DS . 'com_users' . DS . 'login' . DS . 'default_login.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <?php
            echo $this->pane->endPanel();

            $title = JText::_('Plugins');
            echo $this->pane->startPanel($title, 'plugin');
            ?>
            <table class='adminlist'>
                <th>Plugin type</th>
                <th>Name</th>
                <th>Installed</th>
                <th>Enabled</th>
                <!--- a -->
                <tr>
                    <td>
                        <?php echo JText::_('alikonweb'); ?>
                    </td>

                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'acaptcha' . DS . 'acaptcha.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('alikonweb', 'acaptcha'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Ajax Captcha') . '</a>';
                        } else {
                            echo JText::_('Ajax Captcha');
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'acaptcha' . DS . 'acaptcha.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('alikonweb', 'acaptcha')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>           
                <!-- b -->
                <tr>
                    <td>
                        <?php echo JText::_('alikonweb'); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'detector' . DS . 'detector.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('alikonweb', 'detector'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Detector') . '</a>';
                        } else {
                            echo JText::_('Detector');
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'detector' . DS . 'detector.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('alikonweb', 'detector')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>	
                <!--- a -->
                <tr>
                    <td>
                        <?php echo JText::_('alikonweb'); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'googlemap' . DS . 'googlemap.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('alikonweb', 'googlemap'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Map') . '</a>';
                        } else {
                            echo JText::_('Map');
                        }
                        ?>    
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'googlemap' . DS . 'googlemap.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('alikonweb', 'googlemap')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>
                <!--- a -->

       
                <tr>
                    <td>
                        <?php echo JText::_('alikonweb'); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'youtubevideo' . DS . 'youtubevideo.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('alikonweb', 'youtubevideo'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Video') . '</a>';
                        } else {
                            echo JText::_('Video');
                        }
                        ?>    
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'alikonweb' . DS . 'youtubevideo' . DS . 'youtubevideo.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('alikonweb', 'youtubevideo')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>
                         <!--- a -->
                <tr>
                    <td>
                        <?php echo JText::_('contact'); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'contact' . DS . 'detector' . DS . 'detector.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('contact', 'detector'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Detector') . '</a>';
                        } else {
                            echo JText::_('Detector');
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'contact' . DS . 'detector' . DS . 'detector.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('contact', 'detector')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>
                         <!--- a -->
                <tr>
                    <td>
                        <?php echo JText::_('kunena'); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'kunena' . DS . 'detector' . DS . 'detector.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('kunena', 'detector'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Detector') . '</a>';
                        } else {
                            echo JText::_('Detector');
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'kunena' . DS . 'detector' . DS . 'detector.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('kunena', 'detector')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>         
                <tr>
                    <td>
                        <?php echo JText::_('user'); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'user' . DS . 'detector' . DS . 'detector.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('user', 'detector'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Detector') . '</a>';
                        } else {
                            echo JText::_('Detector');
                        }
                        ?>      
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'user' . DS . 'detector' . DS . 'detector.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('user', 'detector')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>

         <!--- a -->
       <tr>
                    <td>
                        <?php echo JText::_('system'); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'system' . DS . 'acaptcha' . DS . 'acaptcha.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('system', 'acaptcha'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Ajax Captcha') . '</a>';
                        } else {
                            echo JText::_('Ajax Captcha');
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'system' . DS . 'acaptcha' . DS . 'acaptcha.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('system', 'acaptcha')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>
                
                         <!--- a -->
       <tr>
                    <td>
                        <?php echo JText::_('system'); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'system' . DS . 'acontact' . DS . 'acontact.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('system', 'acontact'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Contact map & video') . '</a>';
                        } else {
                            echo JText::_('Contact map & video');
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'system' . DS . 'acontact' . DS . 'acontact.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('system', 'acontact')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>  
                       <tr>
                    <td>
                        <?php echo JText::_('system'); ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'system' . DS . 'detector' . DS . 'detector.php')) {
                            $link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . aa4jVersion::getPluginLink('system', 'detector'));
                            echo '<a href="' . $link . '" title="Settings" >' . JText::_('Detector') . '</a>';
                        } else {
                            echo JText::_('Detector');
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JFile::exists(JPATH_SITE . DS . 'plugins' . DS . 'system' . DS . 'detector' . DS . 'detector.php')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:installed' title='status:installed' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status:unistalled' title='status:unistalled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (JPluginHelper::isEnabled('system', 'detector')) {
                            echo "<img src='templates/bluestork/images/admin/tick.png'  alt='status:enabled' title='status:enabled' align='middle' border='0'>";
                        } else {
                            echo "<img src='templates/bluestork/images/admin/publish_x.png'  alt='status' title='status:disabled' align='middle' border='0'>";
                        }
                        ?>
                    </td>
                </tr>  
            </table>
            <?php
            echo $this->pane->endPanel();

            $title = JText::_('Support us');
            echo $this->pane->startPanel($title, 'supportus');
            ?>
            <table class='adminlist'>
                <tr>
                    <td>
                        <p><?php echo JText::_('Support us'); ?></p>
                        <div style="text-align: center;">
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                <input type="hidden" name="cmd" value="_donations">
                                <input type="hidden" name="business" value="nicolagalgano@yahoo.it">
                                <input type="hidden" name="lc" value="en_US">
                                <input type="hidden" name="item_name" value="alikonweb ajax for Joomla">
                                <input type="hidden" name="currency_code" value="EUR">
                                <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
                                <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal secure payments.">
                                <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            </form>
                        </div>
                        <p></p>
                    </td>
                </tr>
                <tr>
                    <td>if you like our extension please vote for us on : <a href="http://extensions.joomla.org/extensions/owner/alikon" rel="nofollow" target="_blank" class="external">joomla extensions</a>
                    </td>	
                </tr>
            </table>
            <?php
            echo $this->pane->endPanel();
            $title = JText::_('Checksum');
            echo $this->pane->startPanel($title, 'checksum');
            if (JFile::exists(JPATH_SITE . DS . 'cli' . DS . 'checksum' . DS . 'logs' . DS . 'jchecksum.php')) {

                $results = stat(JPATH_SITE . DS . 'cli' . DS . 'checksum' . DS . 'logs' . DS . 'jchecksum.php');
                $kb = round($results[size] / 1024, 0);

                echo 'Latest run:' . date('c', $results[mtime]) . '<br>';
                echo "Log file size is " . $kb . " kb<br>";
                $loglink = JRoute::_('index.php?option=com_aa4j&task=logchksum&tmpl=component#bottom');
                $rellink = "{ handler:'iframe', size:{x:800, y:600} }";
                echo '<a href="' . $loglink . '" class="modal" rel="' . $rellink . '">' . JText::_('CLICHKSUMLOG') . '</a>';
            } else {
                echo 'Checksum never runned';
            }
            echo $this->pane->endPanel();
            echo $this->pane->endPane();
            ?>
        </td>
    </tr>
</table>
<form action="index.php" method="post" name="adminForm">
    <input type="hidden" name="option" value="com_aa4j" />
    <input type="hidden" name="c" value="website" />
    <input type="hidden" name="view" value="cpanel" />
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_('form.token'); ?>
</form>
