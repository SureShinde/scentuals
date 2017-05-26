<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Shipping_Info extends Mage_Shipping_Model_Info {
    
    /**
     * Retrieve tracking by tracking entity id
     *
     * @return array
     */
    public function getTrackingInfoByTrackId()
    {

        $track = Mage::getModel('sales/order_shipment_track')->load($this->getTrackId());
        
        if ($track->getId() && $this->getProtectCode() == $track->getProtectCode()) {

            if ($track->getCarrierCode() == 'chcanpost2module') {
                         
                $this->updateTrackingInfo($track);
                
                $this->_trackingInfo = array(array($track));
                
            } else {
                
                $this->_trackingInfo = array(array($track->getNumberDetail()));
                
            }
            
        }
        
        return $this->_trackingInfo;
        
    }
    
    
    /**
     * Retrieve all tracking by order id
     *
     * @return array
     */
    public function getTrackingInfoByOrder()
    {
        
        $shipTrack = array();
        
        $order = $this->_initOrder();
        
        if ($order) {
            
            $shipments = $order->getShipmentsCollection();
            
            foreach ($shipments as $shipment){
                
                $increment_id = $shipment->getIncrementId();
                
                $tracks = $shipment->getTracksCollection();

                $trackingInfos=array();
                
                foreach ($tracks as $track){
                    
                    if ($track->getCarrierCode() == 'chcanpost2module') {
                    
                        $this->updateTrackingInfo($track);
                        
                        $trackingInfos[] = $track;
                    
                    } else {
                    
                        $trackingInfos[] = $track->getNumberDetail();
                    
                    }
                    
                }
                
                $shipTrack[$increment_id] = $trackingInfos;
                
            }
            
        }
        
        $this->_trackingInfo = $shipTrack;
        
        return $this->_trackingInfo;
        
    }
    
    
    /**
     * Retrieve all tracking by ship id
     *
     * @return array
     */
    public function getTrackingInfoByShip()
    {
        
        $shipTrack = array();
        
        $shipment = $this->_initShipment();
        if ($shipment) {
            $increment_id = $shipment->getIncrementId();
            $tracks = $shipment->getTracksCollection();

            $trackingInfos=array();
            foreach ($tracks as $track){
                if ($track->getCarrierCode() == 'chcanpost2module') {

                    $this->updateTrackingInfo($track);

                    $trackingInfos[] = $track;

                } else {

                    $trackingInfos[] = $track->getNumberDetail();

                }
            }
            $shipTrack[$increment_id] = $trackingInfos;

        }
        $this->_trackingInfo = $shipTrack;
        return $this->_trackingInfo;
    }
    
    
    private function updateTrackingInfo($track)
    {
        
        $track_info = Mage::helper('chcanpost2module/rest_tracking')->getDetails($track->getTrackNumber());                

        //$track_info = file_get_contents('/var/www/test/test.xml');

        $xml = new SimpleXMLElement($track_info);

        $progress = array();

        if (!empty($xml->{'significant-events'}->occurrence)) {

            foreach ($xml->{'significant-events'}->occurrence as $event) {

                $progress[] = array(
                    'deliverydate' => $event->{'event-date'},
                    'deliverytime' => $event->{'event-time'},
                    'deliverylocation' => $event->{'event-site'}.', '.$event->{'event-province'},
                    'activity' => $event->{'event-description'}
                );

            }   

            $track->setProgressdetail($progress);

        } else if (!empty($xml->message->description)) {
            
            $track->setTrackSummary(Mage::helper('chcanpost2module')->__('Requested tracking number can not be tracked'));

        }

        $track->setCarrierTitle(Mage::helper('chcanpost2module')->__('Canada Post'));

        $track->setTracking($track->getTrackNumber());     
        
    }
    
}
