<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Option extends Mage_Core_Helper_Abstract
{

    /**
     * @param $quote_params
     * @param $shipment
     * @param $shipping_address
     * @param $mandatory_options
     * @return string
     * @TODO fix hard coded Non-delivery handling codes
     */
    public function composeForOrder($quote_params, $shipment, $shipping_address, $mandatory_options = array(), $available_options = array())
    {
        $options = array();
        
        //check if global total require signature
        if ((int)Mage::getStoreConfig('carriers/chcanpost2module/require_signature') && $shipment->getOrder()->getData('grand_total') > Mage::getStoreConfig('carriers/chcanpost2module/signature_threshhold')) {

            $options['SO'] = array();
            
        } 

        //check if user requested signature        
        if (!isset($options['SO'])) {
            
            $signature = $quote_params['signature'];
            
            if (!empty($signature)) {
                
                $options['SO'] = array();
                
            }
            
        }
        
        //check all order items if any of them require signature
        if (!isset($options['SO'])) {
            
            foreach ($shipment->getAllItems() as $item) {
                
                if ($item->getSignature()) {
                    
                    $options['SO'] = array();
                    
                }
                
            }
            
        }        
        
        if ($quote_params['card_for_pickup']) {
            
            $options['HFP'] = array();
            
        }
        
        if ($quote_params['do_not_safe_drop']) {
            
            $options['DNS'] = array();
            
        }
        
        if ($quote_params['leave_at_door']) {
            
            $options['LAD'] = array();
            
        }

        if (isset($quote_params['COD']) || isset($quote_params['cod'])) {

            $options['COD'] = array(
                'option-qualifier-1' => false, // we send grand totall... we do NOT add shipping to the amount passed in.
                'amount' => round($shipment->getOrder()->getGrandTotal(),2), // total.
            );

        }

        if ($quote_params['coverage'] && !empty($quote_params['coverage_amount'])) {
            
            $options['COV'] = array('amount' => $quote_params['coverage_amount']);
            
        }

        $options = $this->checkAgeSensitiveProducts($shipment->getAllItems(), $shipping_address->getCountryId(), $shipping_address->getRegionCode(), $options);

        if (!empty($quote_params['office_id'])) {
            
            $office = Mage::getModel('chcanpost2module/office')->load($quote_params['office_id']);
            
            $options['D2PO'] = array(
                'option-qualifier-2' => $office->getCpOfficeId(),
            );
             
        }

        if (!empty($mandatory_options)) {
            
            foreach ($mandatory_options as $option_code) {
                
                if (!isset($options[$option_code])) {
                
                    $options[$option_code] = array();
                
                }
                
            }
            
        }

        $conflicted_options = array();
        
        foreach ($options as $code => $data) {
            
            $info = $this->getConflicts($code);
            
            if (!empty($info['conflict'])) {
            
                $conflicted_options = array_unique(array_merge($conflicted_options, $info['conflict']));
            
            }
            
        }

        //remove not available options
        foreach ($options as $code => $data) {
            
            if (!in_array($code, $available_options)) {
                
                unset($options[$code]);
                
            }
            
        }


        if (is_array($conflicted_options)) {
        
            //remove conflicted option, just double check
            foreach ($conflicted_options as $code) {

                unset($options[$code]);

            }
        
        }


        if (count($options) > 1) {
            $optionCodes = array_keys($options);
            // Check if this shipment is a non-delivery.
            if (count(array_intersect($optionCodes, array('RTS', 'RASE', 'ABAN'))) > 1) {
                $nondeliveryPreference = Mage::getStoreConfig('carriers/chcanpost2module/nondelivery_preference');
                // Check if the merchant's preference (could be RTS or ABAN) is one of the options ...
                if (in_array($nondeliveryPreference, $optionCodes)) {
                    // ... if so, choose it (by way of removing all others).
                    foreach ($options as $code => $data) {
                        if ($code != $nondeliveryPreference) {
                            unset($options[$code]);
                        }
                    }
                } else {
                    // ... if not, choose RASE by random default.
                    foreach ($options as $code => $data) {
                        if ($code != 'RASE') {
                            unset($options[$code]);
                        }
                    }
                }
            }
        }

        return $this->formatOptions($options);
        
    }
    
    
    public function composeForCheckout(
            $coverage, 
            $signature, 
            $coverage_amount = 0, 
            $card_for_pickup = false, 
            $do_not_safe_drop = false, 
            $leave_at_door = false,
            $cod = false,
            $dc = false,
            $office_id = 0)
    {

        $options = array();
        
        if ($dc) {
            
            $options['DC'] = array();
            
        }
        
        if ($signature) {
            
            $options['SO'] = array();
            
        }
        
        if ($coverage) {
            
            $options['COV'] = array('amount' => $coverage_amount);
            
        }
        
        if ($card_for_pickup) {
            
            $options['HFP'] = array();
            
        }
        
        if ($do_not_safe_drop) {
            
            $options['DNS'] = array();
            
        }
        
        if ($leave_at_door) {
            
            $options['LAD'] = array();
            
        }

        if ($cod) {

            $options['COD'] = array();

        }

        if (!empty($office_id)) {
            
//            $options['D2PO'] = array(
//                'notification' => '',
//                'option-qualifier-2' => $office_id,
//            );
            
        }
        
        return $options;
        
    }
    
    
    private function checkAgeSensitiveProducts($items, $country_code, $province_code, $options)
    {
        
        foreach ($items as $item) {
            
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            
            $pa = $product->getResource()
                          ->getAttribute('proof_of_age')
                          ->getSource()
                          ->getOptionText($product->getProofOfAge());

            switch ($pa) {
                
                case '18+':
                    
                    $options['PA18'] = array();
                    
                    break;
                    
                case '19+':
                    
                    $options['PA19'] = array();
                    
                    break;
                
                case '18+ or 19+ by province':
                    
                    if ($country_code == 'CA') {
                        
                        $options[$this->ageByProvince($province_code)] = array();
                        
                    }
                    
                    break;
                
                default:
                
                    ;
                    
            }
            
        }
        
        return $options;
        
    }
    
    
    private function ageByProvince($province_code)
    {
        
        $pa = 'PA18';
        
        switch ($province_code) {
            
            case 'BC':
            case 'NB':
            case 'NL':
            case 'NS':
            case 'NT':
            case 'YT':
                
                $pa = 'PA19';
                
                break;
            
        }
        
        return $pa;
        
    }
    
    
    private function formatOptions($options)
    {
        
        $xml = '';
        
        foreach ($options as $option_code=>$params) {
            
            if ($option_code == 'D2PO') {
            
                $xml .= '<option><option-code>'.$option_code.'</option-code>';

                foreach ($params as $param=>$value) {
                        
                    $xml .= '<'.$param.'>'.$value.'</'.$param.'>';
                
                }

                $xml .= '</option>';
            
            } else {
                
                $xml .= '<option><option-code>'.$option_code.'</option-code>';

                if (!empty($params['amount'])) {

                    $xml .= '<option-amount>'.number_format(str_replace(',', '', $params['amount']), 2, '.', '').'</option-amount>';

                }
                /*
                 * COD
                 */
                if (!empty($params['option-qualifier-1'])) {

                    $xml .= '<option-qualifier-1>' . ($params['option-qualifier-1'] == true ? 'true' : 'false') . '</option-qualifier-1>';

                }

                $xml .= '</option>';
                
            }
            
        }
        
        return $xml;
        
    }
    
    
    public function getConflicts($option)
    {
        
        $opt = array(
            'DC' => array('conflict' => array(), 'pre' => ''),
            'SO' => array('conflict' => array('LAD'), 'pre' => 'DC'),
            'COV' => array('conflict' => array(), 'pre' => 'DC'),
            'LAD' => array('conflict' => array('D2PO', 'COD', 'DNS', 'HFP', 'PA18', 'PA19', 'SO'), 'pre' => ''),
            'D2PO' => array('conflict' => array('LAD', 'HFP', 'DNS', 'COD'), 'pre' => ''),
            'COD' => array('conflict' => array('D2PO', 'LAD'), 'pre' => 'DC'),
            'DNS' => array('conflict' => array('PA18', 'PA19', 'D2PO', 'HFP', 'LAD'), 'pre' => ''),
            'HFP' => array('conflict' => array('D2PO', 'LAD', 'DNS'), 'pre' => ''),
            'PA18' => array('conflict' => array('DNS', 'PA19', 'LAD'), 'pre' => 'SO'),
            'PA19' => array('conflict' => array('DNS', 'PA18', 'LAD'), 'pre' => 'SO'),
        );
        
        return (!empty($opt[$option])) ? $opt[$option] : array();
        
    }
    
    
    public function isPaOption($type = 'front', $quote = null)
    {
        
        if ($type == 'front') {
        
            $quote = Mage::getSingleton('checkout/session')->getQuote();
        
        }
        
        $address = $quote->getShippingAddress();
        
        $options = $this->checkAgeSensitiveProducts($quote->getAllItems(), $address->getCountryId(), $address->getPostcode(), array());

        return (isset($options['PA18']) || isset($options['PA19']));
        
    }
    
    
    public function getConflicetedOptions($selected_options)
    {
        
        $options = array();
        
        if ($selected_options['signature']) {
            
            $data = $this->getConflicts('SO');
            
            $options = array_merge($data['conflict'], $options);
            
        }
        
        if ($selected_options['coverage']) {
            
            $data = $this->getConflicts('COV');
            
            $options = array_merge($data['conflict'], $options);
            
        }
        
        if ($selected_options['card_for_pickup']) {
            
            $data = $this->getConflicts('HFP');
            
            $options = array_merge($data['conflict'], $options);
            
        }
        
        if ($selected_options['do_not_safe_drop']) {
            
            $data = $this->getConflicts('DNS');
            
            $options = array_merge($data['conflict'], $options);
            
        }
        
        if ($selected_options['leave_at_door']) {
            
            $data = $this->getConflicts('LAD');
            
            $options = array_merge($data['conflict'], $options);
            
        }

        if ($selected_options['cod']) {

            $data = $this->getConflicts('COD');

            $options = array_merge($data['conflict'], $options);

        }

        if (!empty($selected_options['office_id'])) {
            
            $data = $this->getConflicts('D2PO');
            
            $options = array_merge($data['conflict'], $options);
            
        }
        
        return array_unique($options);
        
    }
    
    
    public function getMaxCoverage($service_code, $country_code)
    {

        $amount = 0;
        
        $xml = Mage::helper('chcanpost2module/rest_service')->getInfo($service_code, $country_code);

        if (!empty($xml->options)) {

            foreach ($xml->options->option as $opt) {

                if ((string)$opt->{'option-code'} == 'COV') {

                    $amount = (float)$opt->{'qualifier-max'};

                    break;

                }

            }

        }
        
        return $amount;
        
    }
    
    
    public function getAvailableOptions($service_code, $country_code)
    {
        
        $options = array();
        
        $xml = Mage::helper('chcanpost2module/rest_service')->getInfo($service_code, $country_code);

        if (!empty($xml->options)) {

            foreach ($xml->options->option as $opt) {
                
                $amount = (float)$opt->{'qualifier-max'};
                
                $options[(string)$opt->{'option-code'}] = array(
                    'code' => (string)$opt->{'option-code'},
                    'max' => ((string)$opt->{'option-code'} == 'COV') ? (float)$opt->{'qualifier-max'} : 0
                );
                
            }
            
        }
        
        return $options;
        
    }
    
}
