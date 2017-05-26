<?php

/* * ****************************************************
 * Package   : Brand
 * Author    : ArexWorks
 * Copyright : (c) 2015
 * ***************************************************** */
?>
<?php

class ARW_Brand_Model_Observer
{

    public function addToTopmenu(Varien_Event_Observer $observer)
    {
        $versionInfo = Mage::getVersionInfo();
        if ((int)$versionInfo['major'] >= 1 && (int)$versionInfo['minor'] >= 7) {
            if (Mage::helper('brand')->addToTopmenu()) {
                $params = Mage::app()->getRequest()->getParams();
                $active = (Mage::app()->getRequest()->getRouteName() == 'brand' ? 'active' : '');
                $menu = $observer->getMenu();
                $tree = $menu->getTree();
                if (Mage::helper('brand')->titleLink() != '') {
                    $name = Mage::helper('brand')->titleLink();
                } else {
                    $name = 'Brand';
                }
                if (Mage::helper('brand')->urlKey() != '') {
                    $urlKey = Mage::helper('brand')->urlKey();
                } else {
                    $urlKey = 'brand';
                }
                $node = new Varien_Data_Tree_Node(array('name' => $name, 'is_active' => $active, 'id' => 'brand', 'url' => Mage::getUrl() . $urlKey), 'id', $tree, $menu);
                $menu->addChild($node);
                $collection = Mage::getModel('brand/brand')->getCollection()
                    ->addFieldToFilter('status', array('eq' => 1))
                    ->setOrder('priority', 'asc');
                foreach ($collection as $brand) {
                    $active = (isset($params['id']) && $brand->getId() == $params['id'] ? 'active' : '');
                    $tree = $node->getTree();
                    $data = array(
                        'name' => $brand->getTitle(),
                        'is_active' => $active,
                        'id' => 'brand-' . $brand->getUrlKey(),
                        'url' => Mage::getUrl() . $urlKey . '/' . $brand->getUrlKey()
                    );
                    $subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
                    $node->addChild($subNode);
                }
            }
        }
    }

}
