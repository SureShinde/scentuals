<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_GetRates extends Collinsharper_Canpost_Helper_Rest_Request
{


    /**
     * API REST call Get Rates
     */
    public function getRates($data)
    {
        if (Mage::getStoreConfig('carriers/chcanpost2module/enable_cache')) {
            $cacheKey = 'chcanpost2module_getrates_' . md5(json_encode($data));
            Mage::helper('chcanpost2module')->log("cache key = {$cacheKey}");

            $cacheData = Mage::app()->getCache()->load($cacheKey);
            if ($cacheData !== false) {
                Mage::helper('chcanpost2module')->log("No request, data from cache, cache key = {$cacheKey}");
                return json_decode($cacheData, true);
            }
        }

        $xml = $this->composeXml($data);

        $url = $this->getBaseUrl().'rs/ship/price';
        
        $headers = array(
            'Accept: application/vnd.cpc.ship.rate+xml',
            'Content-Type: application/vnd.cpc.ship.rate+xml'
        );

        $response = $this->send($url, $xml, false, $headers);

        if (!empty($response) && preg_match('/price-quotes/', $response)) {
            
            $response_xml = new SimpleXMLElement($response);
            
        }

        $data = array();

        if (!empty($response_xml)) {         

            $allowed_methods = explode(',', Mage::getStoreConfig('carriers/chcanpost2module/allowed_methods'));
            
            foreach ($response_xml->children() as $quote) {
                if (in_array((string)$quote->{'service-code'}, $allowed_methods)) {
                
                    // TODO: create setting to include this cost to flat rate (still needs tax, etc.) (and don't forget below):
                    //$optionsCost = 0;
                    //foreach ($quote->{'price-details'}->{'options'}->{'option'} as $option) {
                    //    $optionsCost += (float) $option->{'option-price'};
                    //}
                    $data[(string)$quote->{'service-code'}] = array(
                        'code' => (string)$quote->{'service-code'},
                        'expected-delivery-date' => (string)$quote->{'service-standard'}->{'expected-delivery-date'},
                        'method' => (string)$quote->{'service-name'},
                        'price'  => (float)$quote->{'price-details'}->{'due'},
                        //'options-cost' => (float) $optionsCost,
                    );
                    
                }

            }
            
        } elseif(Mage::getStoreConfig('carriers/chcanpost2module/fail_title') && Mage::getStoreConfig('carriers/chcanpost2module/fail_rate')) {

            if (!empty($response)) {
            
                try {

                    $response_xml = new SimpleXMLElement($response);

                } catch (Exception $e) {

                    Mage::helper('chcanpost2module')->log($e->getMessage());

                }

            }

	    $data['failure'] = array(
		'code' => 'failure',
		'expected-delivery-date' => false,
		'method' => Mage::getStoreConfig('carriers/chcanpost2module/fail_title'),
		'price'  => (float)Mage::getStoreConfig('carriers/chcanpost2module/fail_rate'),
		//'options-cost' => 0
	    );

        }
        
        if (Mage::getStoreConfig('carriers/chcanpost2module/enable_cache') && !$this->error && !empty($data) && !isset($data['failure'])) {
            Mage::helper('chcanpost2module')->log("Response has been saved in cache");
            $cacheData = json_encode($data);
            Mage::app()->getCache()->save($cacheData, $cacheKey, array(), 300);
        }
        
        return $data;
        
    }
    
    
    /**
     * @param $data - array
     * 
     * @return string XML
     */
    public function composeXml($data)
    {

        $xml = new DOMDocument;

        $xml->encoding = 'UTF-8';
        
        $xml->formatOutput = true;

        $locale = Mage::getStoreConfig('carriers/chcanpost2module/locale') == 'FR' ? 'FR' : 'EN';

        $scenario = $xml->createElement('mailing-scenario');

        $scenario->setAttribute('xmlns', $data['xmlns']);

        $contract_number = Mage::getStoreConfig('carriers/chcanpost2module/contract');
        
        if (Mage::getStoreConfig('carriers/chcanpost2module/quote_type') == 'commercial') {
            
            $scenario->appendChild($xml->createElement('customer-number', Mage::getStoreConfig('carriers/chcanpost2module/api_customer_number')));

            if (!empty($contract_number)) {
            
                $scenario->appendChild($xml->createElement('contract-id', $contract_number));
            
            }

            $scenario->appendChild($xml->createElement('quote-type', 'commercial'));
            
        } else {
            
            $scenario->appendChild($xml->createElement('quote-type', 'counter'));
            
        }
        
        if (!empty($data['services']) && is_array($data['services'])) {
            
            $services = $xml->createElement('services');
            
            foreach ($data['services'] as $service_code) {
                
                $services->appendChild($xml->createElement('service-code', $service_code));
                
            }
            
            $scenario->appendChild($services);
            
        }

        // iff they specify a lead time we wioll add in a mailing date
        // if not lets leave it default to whatever CP does.
        //        $mailing_date = date('Y-m-d');
        if((int)Mage::getStoreConfig('carriers/chcanpost2module/lead_time_days'))
        {
            
            $_days = (int)Mage::getStoreConfig('carriers/chcanpost2module/lead_time_days');
            
            $mailing_date = date('Y-m-d',strtotime("+{$_days} days"));
            
            $scenario->appendChild($xml->createElement('expected-mailing-date', $mailing_date));
            
        }
        
        $scenario->appendChild($xml->createElement('origin-postal-code', str_replace(' ', '', strtoupper(Mage::getStoreConfig('shipping/origin/postcode')))));
        
        // Parcel characteristics
        $parcel = $xml->createElement('parcel-characteristics');
        
        $parcel->appendChild($xml->createElement('weight', $data['weight']));

        $dim = $xml->createElement('dimensions');
                
        if (!empty($data['box'])) {

            $dim->appendChild($xml->createElement('length', $data['box']['l']));

            $dim->appendChild($xml->createElement('width', $data['box']['w']));

            $dim->appendChild($xml->createElement('height', $data['box']['h']));

        } else {
            
            $dim->appendChild($xml->createElement('length', Mage::helper('chunit')->getConvertedLength(Mage::getStoreConfig('carriers/chcanpost2module/default_l'), Mage::getStoreConfig('catalog/measure_units/length'), 'cm')));

            $dim->appendChild($xml->createElement('width', Mage::helper('chunit')->getConvertedLength(Mage::getStoreConfig('carriers/chcanpost2module/default_w'), Mage::getStoreConfig('catalog/measure_units/length'), 'cm')));

            $dim->appendChild($xml->createElement('height', Mage::helper('chunit')->getConvertedLength(Mage::getStoreConfig('carriers/chcanpost2module/default_h'), Mage::getStoreConfig('catalog/measure_units/length'), 'cm')));

        }
        
        $parcel->appendChild($dim);
        
        $scenario->appendChild($parcel);
        
        // Destination parameters
        $destination = $xml->createElement('destination');
        
        switch ($data['country_code']) {
            
            case 'US':
                
                $us = $xml->createElement('united-states');

                $us->appendChild($xml->createElement('zip-code', $this->formatPostalCode($data['postal-code'])));

                $destination->appendChild($us);  
                
                break;
            
            case 'CA':
                    
                $domestic = $xml->createElement('domestic');

                $postcode = (!empty($data['office_id'])) ? Mage::getModel('chcanpost2module/office')->load($data['office_id'])->getPostalCode() : $data['postal-code'];

                $domestic->appendChild($xml->createElement('postal-code', str_replace(' ', '', $this->formatPostalCode($postcode))));

                $destination->appendChild($domestic);  
                
                break;
            
            default:
                
                $international = $xml->createElement('international');

                $international->appendChild($xml->createElement('country-code', $data['country_code']));

                $destination->appendChild($international);  
                
                break;
            
        }

        $scenario->appendChild($destination);

        $data['coverage'] = (!empty($data['coverage']));

        $data['signature'] = (!empty($data['signature']));
        
        $data['card_for_pickup'] = (!empty($data['card_for_pickup']));
        
        $data['do_not_safe_drop'] = (!empty($data['do_not_safe_drop']));
        
        $data['leave_at_door'] = (!empty($data['leave_at_door']));

        $data['cod'] = (!empty($data['cod']));

        if (empty($data['coverage_amount'])) { 
            
            $data['coverage_amount'] = 0;

        }

        if (!empty($data['services'][0])) {
        
            $service_info = Mage::helper('chcanpost2module/rest_service')->getInfo($data['services'][0], $data['country_code']);
        
        }
        
        $data['dc'] = false;
        
        if (!empty($service_info->options->option)) {
            
            $mandatory_options = array();
            
            foreach ($service_info->options->option as $opt) {
                
                if (strtolower((string)$opt->mandatory) == 'true' && (string)$opt->{'option_code'} == 'DC') {
                    
                    $data['dc'] = true;
                    
                    break;
                    
                }
                
            }
            
        }
        
        $options_data = Mage::helper('chcanpost2module/option')->composeForCheckout(
                $data['coverage'], 
                $data['signature'], 
                $data['coverage_amount'], 
                $data['card_for_pickup'],
                $data['do_not_safe_drop'],
                $data['leave_at_door'],
                $data['cod'],
                $data['dc'],
                $data['office_id']
                );
        
        if (!empty($options_data)) {
        
            $options = $xml->createElement('options');

            foreach ($options_data as $option_code=>$params) {

                $option = $xml->createElement('option');

                $option->appendChild($xml->createElement('option-code', $option_code));


                if (!empty($params['COD'])) {

                    //$params['amount'] = '';

                }

                if (!empty($params['amount'])) {

                    $option->appendChild($xml->createElement('option-amount', number_format(str_replace(',', '', $params['amount']), 2, '.', '')));

                }


                $options->appendChild($option);

            }

            $scenario->appendChild($options);        

        }
        
        $xml->appendChild($scenario);

        return $xml->saveXML();
        
    }    
    
    
}
