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

class ARW_Sebian_Adminhtml_SupportController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('arw/sebian')
            ->_title(Mage::helper('adminhtml')->__('Help & Support'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Help & Support'), Mage::helper('adminhtml')->__('Help & Support'));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }
} 
?>
