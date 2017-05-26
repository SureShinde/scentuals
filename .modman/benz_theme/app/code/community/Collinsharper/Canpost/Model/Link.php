<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Link extends Mage_Core_Model_Abstract
{
    
    
    protected function _construct() {
        
        $this->_init('chcanpost2module/link');
        
    } 
    
    
    public function getLabelDataByOrderId($order_id, $type='label')
    {
        
        $collection = Mage::getModel('chcanpost2module/link')->getCollection()
                        ->addFieldToFilter('s.order_id', $order_id)
                        ->addFieldToFilter('rel', $type);
                        
        $collection->getSelect()->joinLeft(
                    array('s' => Mage::getSingleton('core/resource')->getTableName('ch_canadapost_shipment')),
                    'main_table.cp_shipment_id=s.id',
                    array()
                );              
    
	$data = array();

	foreach ($collection as $item) {

		$data[] = $item->getData();
	
	}

	return $data;
        
    }
    
    
    public function getManifests()
    {
        
        $collection = Mage::getModel('chcanpost2module/link')->getCollection()
                ->addFieldToFilter('rel', 'manifest');
                
        $collection->getSelect()->joinLeft(
                array('ch_chipment'=>Mage::getSingleton('core/resource')->getTableName('ch_canadapost_shipment')),
                'main_table.cp_shipment_id=ch_chipment.id',
                array('order_id' => 'ch_chipment.order_id')
                );
        
        return $collection;
        
    }
    
    public function fetchDetails()
    {
        $responseXml = Mage::helper('chcanpost2module/rest_request')->send($this->getUrl(), "", false, array('Accept: ' . $this->getMediaType()));
        $response = new SimpleXMLElement($responseXml);
        return $response;
    }
}
