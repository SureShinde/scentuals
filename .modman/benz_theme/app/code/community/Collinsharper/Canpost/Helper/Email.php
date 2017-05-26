<?php

/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Email extends Mage_Core_Helper_Abstract {

    
    /**
     * Send email about return
     * 
     * @param string $email
     * @param array $customer_info
     * @return array 
     */
    public function sendReturn($email, $name='', $return_label_url, $shipment, $customer_info = array()) {

        $result = array(
            'error' => '',
            'success' => true,
        );
                  
        if (!empty($email)) {
        
            try {

                $templateId = 'collinsharper_canpost_return_sent_template';

                $mailTemplate = Mage::getModel('core/email_template');

                $mailSubject = 'subj';

                $sender = array(
                    'email' => Mage::getStoreConfig('trans_email/ident_general/email'), 
                    'name' =>  Mage::getStoreConfig('trans_email/ident_general/name')
                    );

                $vars['return_label_url'] = $return_label_url;
                
                $vars['store'] = Mage::app()->getStore($shipment->getStoreId());
                
                $vars['shipment'] = $shipment;
                
                $vars['order'] = $shipment->getOrder();

                $mailTemplate->sendTransactional($templateId, $sender, $email, $name, $vars, $shipment->getStoreId());

            } catch (Exception $e) {                

                $result['success'] = false;

                $result['error'] = $e->getMessage();

            }
        
        } else {
            
            $result = array(
                'error' => Mage::helper('chcanpost2module')->__('email can not be empty'),
                'success' => false,
            );
            
        }
        
        return $result;
        
    }
    
}