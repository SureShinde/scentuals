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
class ARW_Sebian_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

}