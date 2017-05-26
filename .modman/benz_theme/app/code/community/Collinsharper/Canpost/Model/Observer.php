<?php
/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Observer
{


    protected $_block;
    public function adminhtmlWidgetContainerHtmlBefore($event)
    {

        $this->_block = $block = $event->getBlock();

        $this->_handleShippingButton($block);
        $this->_handleOrderButton($block);
    }

    public function _handleOrderButton($block)
    {
        //            $block->addButton('do_something_crazy', array(
//                'label'     => Mage::helper('module')->__('Button'),
//                'onclick'   => "setLocation('{some location}')",
//                'class'     => 'go'
//            ));


        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View ||  (class_parents($block) && get_parent_class($block) == 'Mage_Adminhtml_Block_Sales_Order_View')) {

            $order = $block->getOrder();

            $shipping_method = $order->getShippingMethod();

            if (preg_match('/^chcanpost2module_/', $shipping_method)) {

                $block->removeButton('order_ship');

                //do this just to save order of buttons - it is added back soon
                $block->removeButton('order_reorder');

                $shipped = 0;
                $ordered = 0;

                foreach ($order->getAllItems() as $item) {

                    $shipped += $item->getQtyShipped();

                    $ordered += $item->getQtyOrdered();

                }

                if ($this->_isAllowedAction('ship') && $order->canShip()
                    && !$order->getForcedDoShipmentWithInvoice() && $ordered > $shipped) {
                    $block->addButton('approve_order_ship', array(
                        'label'     => Mage::helper('chcanpost2module')->__('Approve Shipment'),
                        'onclick'   => 'setLocation(\'' . $this->getShipUrl() . '\')',
                        'class'     => 'go'
                    ));
                }

                if ($this->_isAllowedAction('ship') && (int)Mage::getModel('chcanpost2module/shipment')->getShipmentByOrderId($order->getId())->getId() == 0
                    && !$order->getForcedDoShipmentWithInvoice() && $order->getShipmentsCollection()->getSize() > 0) {
                    $block->addButton('cancel_order_ship', array(
                        'label'     => Mage::helper('chcanpost2module')->__('Cancel Shipment'),
                        'onclick'   => 'setLocation(\'' . $this->getCancelShipUrl() . '\')',
                        'class'     => 'go'
                    ));
                }

                if ($this->_isAllowedAction('reorder')
                    && $this->helper('sales/reorder')->isAllowed($order->getStore())
                    && (
                        (method_exists($order,'canReorderIgnoreSalable') && $order->canReorderIgnoreSalable())
                        || (method_exists($order,'canReorder') && $order->canReorder()) //for compatability with v.1.6 of magento
                    )
                ) {
                    $block->addButton('order_reorder', array(
                        'label'     => Mage::helper('sales')->__('Reorder'),
                        'onclick'   => 'setLocation(\'' . $this->getReorderUrl() . '\')',
                        'class'     => 'go'
                    ));
                }

            }

        }


    }

    public function _handleShippingButton($block)
    {

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Shipment_View || (class_parents($block) && get_parent_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Shipment_View')) {

            $shipping_method = $block->getShipment()->getOrder()->getShippingMethod();

            if (preg_match('/^chcanpost2module_/', $shipping_method)) {
                if ($block->getShipment()->getId() && $block->getShipment()->getShippingAddress()->getCountryId() == 'CA') {

                    $cp_shipment = Mage::getModel('chcanpost2module/shipment')->getShipmentByOrderId($block->getShipment()->getOrderId());

                    $label_link = Mage::getModel('chcanpost2module/link')->getCollection()
                        ->addFieldToFilter('cp_shipment_id', $cp_shipment->getId())
                        ->addFieldToFilter('rel', 'label')
                        ->getFirstItem()->getData();
                    if (!empty($label_link)) {

                        $block->addButton('print_label', array(
                                'label'     => Mage::helper('chcanpost2module')->__('Print CP Label'),
                                'class'     => 'save',
                                'onclick'   => 'setLocation(\''.$block->getUrl('*/shipping/label', array('shipment_id' => $block->getShipment()->getId())).'\')',
                            )
                        );

                    }

                    $manifest_status = $cp_shipment->getManifestStatus();

                    if ($block->getShipment()->getShippingAddress()->getData('country_id') == 'CA' && $manifest_status == 'transmitted') {

                        $block->addButton('create_return', array(
                                'label'     => Mage::helper('chcanpost2module')->__('Send Return Label'),
                                'class'     => 'save',
                                'onclick'   => 'setLocation(\''.$block->getUrl('*/shipping/return', array('shipment_id' => $block->getShipment()->getId())).'\')'
                            )
                        );

                    }

                }

            }
        }

    }

    public function updateShippingCosts()
    {
        Mage::helper('chcanpost2module')->log(__METHOD__ . __LINE__);
        $res = Mage::getSingleton('core/resource');
        $read = $res->getConnection('core_read');
        $s_table = $res->getTablename('sales_flat_shipment');
        $o_table = $res->getTablename('sales_flat_order');
        $sql = "select s.entity_id from {$o_table} o, {$s_table} s where s.order_id = o.entity_id and o.shipping_method like 'chcanpost2module_%' and (s.shipment_cost is null or s.shipment_cost = 0) and o.created_at >  DATE_SUB(now(),INTERVAL 16 DAY)";

        $shipment_ids = $read->fetchAll($sql);
        foreach($shipment_ids as $srow) {
            try {
                $shipment = Mage::getModel('sales/order_shipment')->load($srow['entity_id']);
                $x = Mage::getModel('chcanpost2module/shipment')->getShipmentById($srow['entity_id']);
                $shipcost = $x->fetchShipmentPrice();

            } catch (Exception $e) {
                mage::log(__METHOD__ . " Exception in loading shipment or getting cost ". $e->getMessage());
            }

            if(!$shipcost || !$shipment || !$shipment->getId()) {
                continue;
            }


            try {
                $shipment->setShipmentCost($shipcost)->save();

            } catch (Exception $e) {
                mage::log(__METHOD__ . " Exception in saving cost to shipment ". $e->getMessage());
            }
        }
    }


    public function saveOptions($observer)
    {

        $quote = $observer->getEvent()->getQuote();

        $signature = $observer->getEvent()->getRequest()->getParam('signature', 0);

        $coverage = $observer->getEvent()->getRequest()->getParam('coverage', 0);

        $card_for_pickup = $observer->getEvent()->getRequest()->getParam('card_for_pickup', 0);

        $do_not_safe_drop = $observer->getEvent()->getRequest()->getParam('do_not_safe_drop', 0);

        $leave_at_door = $observer->getEvent()->getRequest()->getParam('leave_at_door', 0);

        $cod = $observer->getEvent()->getRequest()->getParam('cod', 0);

        $deliver_to_post_office = $observer->getEvent()->getRequest()->getParam('deliver_to_post_office', 0);

        $office_id = ($deliver_to_post_office) ? $observer->getEvent()->getRequest()->getParam('office', NULL) : NULL;

        $quote_param = Mage::getModel('chcanpost2module/quote_param')
            ->updateForQuote(
                $quote->getId(),
                (int)!empty($signature),
                (int)!empty($coverage),
                0,
                (int)!empty($card_for_pickup),
                (int)!empty($do_not_safe_drop),
                (int)!empty($leave_at_door),
                (int)!empty($cod),
                $office_id
            );

        Mage::helper('chcanpost2module/office')->updateShippingAddress($quote, $deliver_to_post_office, $office_id);

    }


    public function saveAdminOptions($observer)
    {

        $quoteAddress = $observer->getQuoteAddress();

        $quote = $quoteAddress->getQuote();

        $params = Mage::getModel('chcanpost2module/quote_param')->getParamsByQuote($quote->getId());

        if (!empty($params['office_id'])) {

            Mage::helper('chcanpost2module/office')->updateShippingAddress($quote, 1, $params['office_id']);

        }

    }


    public function updateOneStepCheckoutTemplate($observer)
    {

        $block = $observer->getLayout()->getBlock('choose-shipping-method');

        if ($block instanceof Mage_Checkout_Block_Onepage_Shipping_Method_Available) {

            Mage::helper('chcanpost2module')->log(__METHOD__ . "Observer ".get_class($block));

            $block->setTemplate('collinsharper/canpost/onestepcheckout/shipping_method.phtml');

        }

        $block = $observer->getLayout()->getBlock('checkout.cart.shipping');

        // we do not really need change the shipping template - but we do need to change the rates returned.
        if ($block instanceof Mage_Checkout_Block_Cart_Shipping) {

            $x = new Collinsharper_Canpost_Block_Cart_Shipping;
            $x->setData($block->getData());

            $observer->getLayout()->setBlock('checkout.cart.shipping', $x);
//            $block->setTemplate('collinsharper/canpost/cart/shipping.phtml');

        }

    }

    public function updateParams($observer) {

        $order = $observer->getEvent()->getOrder();

        $item = Mage::getModel('chcanpost2module/quote_param')->getByQuoteId($order->getQuoteId());

        // KL: If the order generated by the API, there will be no quote_param record
        if ($item->getId()) {
            $item->setMagentoOrderId($order->getId())->save();
        }

    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/' . $action);
    }

    public function getCancelShipUrl() {

        return $this->getUrl('*/sales_order_shipment/cancel');

    }

    public function getReorderUrl()
    {
        return $this->getUrl('*/sales_order_create/reorder');
    }

    public function helper($x) {

        return Mage::helper($x);

    }

    public function getShipUrl()
    {
        return $this->getUrl('*/sales_order_shipment/start');
    }


    public function getUrl($params='', $params2=array())
    {
        $params2['order_id'] = $this->getOrderId();
        return $this->_block->getUrl($params, $params2);
    }

    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    public function getOrder()
    {
        return Mage::registry('sales_order');
    }



}

