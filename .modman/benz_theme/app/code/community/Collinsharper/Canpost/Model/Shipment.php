<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Shipment extends Mage_Core_Model_Abstract
{
    
    
    protected function _construct() {
        
        $this->_init('chcanpost2module/shipment');
        
    } 
    
    
    public function getShipmentByOrderId($order_id)
    {
        
        $cp_shipment = Mage::getModel('chcanpost2module/shipment')->getCollection()
                                        ->addFieldToFilter('order_id', $order_id);
        
        $cp_shipment->getSelect()
                    ->joinLeft(array('m' => Mage::getSingleton('core/resource')->getTableName('ch_canadapost_manifest')), 'main_table.manifest_id=m.id', array('manifest_status' => 'm.status'));

        return $cp_shipment->getFirstItem();
        
    }
    
    public function getShipmentById($shipment_id)
    {
        
        $cp_shipment = Mage::getModel('chcanpost2module/shipment')->getCollection()
                                        ->addFieldToFilter('magento_shipment_id', $shipment_id);

        return $cp_shipment->getFirstItem();
        
    }


    public function getExpiredCollection()
    {

        $collection = $this->getCollection();

        $collection->getSelect()
            ->joinLeft(
            array('s'=>Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment')),
            'main_table.magento_shipment_id = s.entity_id',
            array(
                'shipment_increment_id' => 's.increment_id',
                'created_at' => 's.created_at',
                'total_qty' => 's.total_qty'
            )
        )
            ->joinLeft(
            array('o'=>Mage::getSingleton('core/resource')->getTableName('sales_flat_order')),
            's.order_id = o.entity_id',
            array(
                'order_increment_id' => 'o.increment_id',
                'order_created_at' => 'o.created_at',
                'ordered_by' => 'CONCAT(o.customer_firstname, \' \',o.customer_lastname)',
            )
        )
            ->joinLeft(
            array('p'=>Mage::getSingleton('core/resource')->getTableName('ch_canadapost_quote_param')),
            'o.quote_id = p.magento_quote_id',
            array('est_delivery_date' => 'p.est_delivery_date')
        );

        return $collection;
    }

    public function getExpired()
    {
        
        $collection = $this->getExpiredCollection();


       $collection->addFieldToFilter('is_delivered', 0);

       $collection->addFieldToFilter('is_checked', 0);

        $collection->addFieldToFilter('p.est_delivery_date', array('lt' => time()));
        

//         $collection->getSelect()
//             ->where('(is_delivered = 0');

        Mage::helper('chcanpost2module')->log(__METHOD__ . __LINE__ . " " . $collection->getSelect()->__toString());
        return $collection;
        
    }
    
    /**
     * Makes a request to Canada Post for this shipment's price details and
     * returns the final price of the shipment as a float.
     * @return number
     */
    public function fetchShipmentPrice()
    {
        return (float) $this->fetchLink('price')->{'due-amount'};
    }
    
    public function fetchLink($rel = 'self')
    {
        return $this->getLink($rel)->fetchDetails();
    }
    
    /**
     * @return Collinsharper_Canpost_Model_Link
     */
    public function getLink($rel = 'self')
    {
        $collection = Mage::getModel('chcanpost2module/link')->getCollection()
            ->addFieldToFilter('cp_shipment_id', $this->getId())
            ->addFieldToFilter('rel', $rel);
        
        if (count($collection) < 1) {
            throw new Exception("Tried to load '{$rel}' link for shipment #{$this->getId()} but could not find one.");
        }
        
        return $collection->getFirstItem();
    }
}
