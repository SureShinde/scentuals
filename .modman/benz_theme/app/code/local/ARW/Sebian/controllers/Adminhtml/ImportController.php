<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     ARM
 * @package      ARM_Themes
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 ArexMage.com. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       ArexMage.com
 * @email        support@arexmage.com
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
class ARW_Sebian_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/sebian/"));
    }
    public function blocksAction() {
        $config = Mage::helper('sebian')->getCfg('install/overwrite_blocks');
        Mage::getSingleton('sebian/import_cms')->importCmsItems('cms/block', 'blocks', $config);
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/sebian/"));
    }
    public function pagesAction() {
        $config = Mage::helper('sebian')->getCfg('install/overwrite_pages');
        Mage::getSingleton('sebian/import_cms')->importCmsItems('cms/page', 'pages', $config);
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/sebian/"));
    }
    public function widgetsAction() {
        Mage::getSingleton('sebian/import_cms')->importWidgetItems('widget/widget_instance', 'widgets', false);
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/sebian/"));
    }
}
