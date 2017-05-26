<?php

/**
 * Collinsharper Canada Post 2.0 Module
 *
 * PHP version 5
 *
 * @category Shipping
 * @package  Collinsharper.Canpost
 * @author   Collins Harper <ch@collinsharper.com>
 * @license  http://collinsharper.com Proprietary License
 * @link     http://collinsharper.com
 */

/**
 * Collinsharper_Canpost_Model_Carrier_Api
 *
 * @category Shipping
 * @package  Collinsharper.Canpost
 * @author   Collins Harper <ch@collinsharper.com>
 * @license  http://collinsharper.com Proprietary License
 * @link     http://collinsharper.com
 */
class Collinsharper_Canpost_Model_Carrier_Api extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_code = 'chcanpost2module';

    //protected $_layers = 1;

    private $fail_reason = '';


    public function isShippingLabelsAvailable() {

        return !(Mage::app()->getRequest()->getControllerName() == 'sales_order_shipment' && Mage::app()->getRequest()->getActionName() == 'view');

    }


    public function getContainerTypes(Varien_Object $params = null) {

        $boxes = array('custom' => 'Custom Box');
        if (Mage::helper('core')->isModuleEnabled('Collinsharper_ShippingBox') &&
            Mage::helper('chbox')->isActive()) {

            $boxes = Mage::getModel('chbox/box')->getContainerTypes();

            if(Mage::registry('current_shipment')) {
                $shipment = Mage::registry('current_shipment');

            }
        }


        return $boxes;

    }


    public function requestToShipment(Mage_Shipping_Model_Shipment_Request $request) {

        return new Varien_Object(array(
            'info'   => array()
        ));

    }


    /**
     * Calculates rates for all available shipping methods under this service.
     *
     * @param Mage_Shipping_Model_Rate_Request $request The shipping rate request object.
     *
     * @return Mage_Shipping_Model_Rate_Result|boolean
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {

        if (!$this->isActive()) {

            return false;

        }

        // 20150102 - if we have no postal code, we do not try for rates.
        if(($request->getDestCountryId() == 'CA' || $request->getDestCountryId() == 'US' ) && ! $request->getDestPostcode()) {
            return false;
        }

        $this->_rawRequest = $request;

        $weight = $request->getPackageWeight();

        //if ($this->_layers >= 1) return false;

        $backordered_no_delivery_estimate = $this->shouldSkipBackOrder($request);

        $packages = Mage::helper('chcanpost2module')->getBoxForItems($request->getAllItems());
        if (!isset($packages['error'])) {
            $rates = $this->getIntersectRates($packages, $request);
        }

        // we didnt get packages or we do not have rates.
        if(isset($packages['error']) || !$rates) {

            if(isset($packages['error'])) {
                Mage::logException(new Exception("Could not pack items into boxes due to error: '{$packages['error']}'"));
            }

            $failRate = Mage::getStoreConfig('carriers/chcanpost2module/fail_rate');
            $failTitle = Mage::getStoreConfig('carriers/chcanpost2module/fail_title');
            if ($failRate && $failTitle) {
                $rates = array(
                    'failure' => array(
                        'code' => 'failure',
                        'expected-delivery-date' => false,
                        'method' => $failTitle,
                        'price'  => (float) $failRate
                    )
                );
            } else {
                $errorMsg = Mage::getStoreConfig('carriers/chcanpost2module/specificerrmsg');
                if (!$errorMsg) {
                    $errorMsg = Mage::helper('chcanpost2module')->__("The items you have selected cannot be shipped at the moment.  Please contact the store owner to arrange for shipping.");
                }

                if(!Mage::getStoreConfig('carriers/chcanpost2module/graceful_failure')) {
                    Mage::getSingleton('core/session')->addError($errorMsg);
                }

                return false;
            }
        }

        $result = Mage::getModel('shipping/rate_result');

        $free_methods = explode(',', Mage::getStoreConfig('carriers/chcanpost2module/free_method'));

        $rates = $this->restrictRatesByProduct($rates, $request);

        $subtotal = $request->getPackageValueWithDiscount();


        if (!empty($rates) && is_array($rates)) {

            if(Mage::helper('chcanpost2module')->isTableRatesCustomerGroup()) {
                $tableRates = Mage::getSingleton('collinsharper_tablerate/tablerate')->getFilteredRates($request);
            }

            foreach ($rates as $rate) {

                if (!empty($tableRates) && empty($tableRates[$rate['code']])) {
                    continue;
                }

                $shippingPrice = $this->getMethodPrice($rate['price'], $rate['code']);

                $title = $rate['method'];

                // get province or state
                // iff ! in list
                $destCountry = $request->getDestCountryId();
                $destProvince = $request->getDestRegionCode();
                $skip_free_region = false;
                if ($destCountry == 'US' || $destCountry == 'CA') {
                    $skip_free_list = array();
                    if ($destCountry == 'US' && $this->getConfigData('exclude_states')) {
                        $skip_free_list = explode(",", $this->getConfigData('exclude_states'));
                    }

                    if ($destCountry == 'CA' && $this->getConfigData('exclude_provinces')) {
                        $skip_free_list = explode(",", $this->getConfigData('exclude_provinces'));
                    }

                    if (count($skip_free_list) > 0) {
                        $skip_free_region = (bool) in_array($destProvince, $skip_free_list);
                    }
                }

                // TODO: This if-sequence is rather convoluted and can use polishing, but it's not a priority.
                $qualifiesForFlatRateOrFree = false;
                if ($this->getConfigData('flat_rate_for_canada_enable')
                    && $request->getDestCountryId() == 'CA'
                    && in_array($rate['code'], array('DOM.RP', 'DOM.EP'))
                ) {
                    if ((Mage::getStoreConfig('carriers/chcanpost2module/quote_type') == 'counter' && $rate['code'] != 'DOM.RP')
                        || (Mage::getStoreConfig('carriers/chcanpost2module/quote_type') == 'commercial' && $rate['code'] != 'DOM.EP')
                    ) {
                        continue;
                    }
                    $qualifiesForFlatRateOrFree = true;
                    $shippingPrice = $this->getConfigData('flat_rate_for_canada');
                    if ($this->getConfigData('flat_rate_for_canada_title')) {
                        $title = $this->getConfigData('flat_rate_for_canada_title');
                    }
                } else if ($this->getConfigData('flat_rate_for_xpresspost_enable')
                    && $request->getDestCountryId() == 'CA'
                    && $rate['code'] == 'DOM.XP'
                ) {
                    $qualifiesForFlatRateOrFree = true;
                    $shippingPrice = $this->getConfigData('flat_rate_for_xpresspost');
                    if ($this->getConfigData('flat_rate_for_xpresspost_title')) {
                        $title = $this->getConfigData('flat_rate_for_xpresspost_title');
                    }
                } else if ($this->getConfigData('flat_rate_for_usaep_enable')
                    && $request->getDestCountryId() == 'US'
                    && $rate['code'] == 'USA.EP'
                ) {

                    $qualifiesForFlatRateOrFree = true;

                    $shippingPrice = $this->getConfigData('flat_rate_for_usaep');

                    if ($this->getConfigData('flat_rate_for_usaep_title')) {

                        $title = $this->getConfigData('flat_rate_for_usaep_title');

                    }


                } else if ($this->getConfigData('flat_rate_for_intxp_enable')
                    && $rate['code'] == 'INT.XP'
                ) {

                    $qualifiesForFlatRateOrFree = true;

                    $shippingPrice = $this->getConfigData('flat_rate_for_intxp');

                    if ($this->getConfigData('flat_rate_for_intxp_title')) {

                        $title = $this->getConfigData('flat_rate_for_intxp_title');

                    }

                }

                if (!$skip_free_region && ($request->getFreeShipping() == true
                        || $request->getPackageQty() == $this->getFreeBoxes()
                        || ($this->getConfigData('free_shipping_enable')
                            && in_array($rate['code'], $free_methods)
                            && (float) $subtotal >= (float) $this->getConfigData('free_shipping_subtotal'))
                    )) {
                    /*
                                        if ((Mage::getStoreConfig('carriers/chcanpost2module/quote_type') == 'counter' && $rate['code'] == 'DOM.EP')
                                            || (Mage::getStoreConfig('carriers/chcanpost2module/quote_type') == 'commercial' && $rate['code'] == 'DOM.RP')
                                        ) {
                                            continue;
                                        }
                    */
                    $qualifiesForFlatRateOrFree = true;
                    $shippingPrice = '0.00';
                }

                if (!$qualifiesForFlatRateOrFree && $request->getDestCountryId() == 'CA'
                    && ($this->getConfigData('flat_rate_for_canada_enable')
                        || $this->getConfigData('flat_rate_for_xpresspost_enable'))
                ) {
                    /*
                     *  TODO stharper : (perm) When client enabled EX/RP and Express Flat rates with a CAD zip then
                     * those rates would be the only rates shown there would not be any live rates. The "continue"
                     * here was causing the conflict. There should be an additional condition that says on fail of
                     * flat rate disable CP, should we present live rates if flat rates do not work. Show live rates
                     * and or show a failure rate.
                     */
                    //continue;
                }

                if (!empty($tableRates) && !empty($tableRates[$rate['code']])) {
                    $shippingPrice = $tableRates[$rate['code']]['price'];
                    if ($tableRates[$rate['code']]['method_title']) {
                        $title = Mage::helper('chcanpost2module')->__($tableRates[$rate['code']]['method_title']);
                    }

                    $method_title_for_display = Mage::helper('chcanpost2module')->__($tableRates[$rate['code']]['method_title_for_display']);

                }

                $method = Mage::getModel('shipping/rate_result_method');

                $method->setCarrier($this->_code);

                $method->setCarrierTitle($this->getConfigData('title'));

                if (!empty($rate['expected-delivery-date'])) {

                    $date = Mage::helper('chcanpost2module')->formatDate($rate['expected-delivery-date']);

                    $full_title = Mage::helper('chcanpost2module')->__('%s - Est. Delivery %s', $title, $date);

                } else {

                    $method->setEstDeliveryDate(0);

                }

                if (Mage::getStoreConfig('carriers/chcanpost2module/show_delivery_date') && !$backordered_no_delivery_estimate && !empty($rate['expected-delivery-date'])) {

                    $title = $full_title;

                }

                $method->setMethodTitle($title);

                $method->setMethod($rate['code']);

                if(!empty($method_title_for_display)) {
                    $method->setMethodDescription($method_title_for_display);
                }

                $method->setCost($rate['price']);

                $method->setPrice($shippingPrice);

                $result->append($method);

            }

        } else if (!empty($this->fail_reason) && $this->fail_reason == 'weight') {

            $error = Mage::getModel('shipping/rate_result_error');

            $error->setCarrier($this->_code);

            $error->setCarrierTitle($this->getConfigData('title'));

            $error->setErrorMessage(Mage::helper('chcanpost2module')->__('Your parcel seems to be too heavy for Canada Post or an item has an unknown weight.'));

            $result->append($error);

        }

        return $result;

    }

    /**
     * Removes from $rates shipping methods that are not allowed by the product items in $quote
     * Uses cpv2 custom product attributes restrict_shipping_methods (boolean) and allowed_shipping_methods (comma-separated string).
     *
     * @param array $rates
     * @param Mage_Shipping_Model_Rate_Request $quote
     * @return array $rates
     */
    public function restrictRatesByProduct($rates, $request)
    {
        $items = $request->getAllItems();
        $products = array();
        foreach ($items as $i) {
            // load the product to get attribute data
            $p = $i->getProduct()->load($i->getProductId());
            if (!$p->getRestrictShippingMethods()) {
                continue;
            }

            $allowedMethods = explode(',', $p->getAllowedShippingMethods());

            foreach ($rates as $code => $rate) {
                if (!in_array($code, $allowedMethods)) {
                    unset($rates[$code]);
                }
            }
        }

        return $rates;
    }

    /**
     * Determines whether there are backordered items that shouldn't get a delivery estimate.
     *
     * @param Mage_Shipping_Model_Rate_Request $request The shipping rate request object.
     *
     * @return boolean
     */
    public function shouldSkipBackOrder($request)
    {
        if (!Mage::getStoreConfig('carriers/chcanpost2module/back_order_no_estimate')) {
            return false;
        }
        // test over the prods and ensure non are backroderd.

        foreach ($request->getAllItems() as $item) {

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            $cbo = $item->getBackorders();
            $qty  = (int) $item->getQty();

            // if it is out and it can back order. we want to flag this as not to show the date.
            if ($cbo && $qty < 1) {
                return true;
            }
        }
        return false;
    }


    /**
     * Gets the title of a shipping method based on its code.
     *
     * @param string $type Should always be 'method' I guess.
     * @param string $code The shipping method code to search for, e.g. 'DOM.RP'
     *
     * @return string|boolean
     */
    public function getCode($type, $code='')
    {
        $codes = array(

            'method'=>array(
                //canada
                'DOM.EP' => Mage::helper('chcanpost2module')->__('Expedited Parcel'),
                'DOM.LIB' => Mage::helper('chcanpost2module')->__('Library Books'),
                'DOM.PC' => Mage::helper('chcanpost2module')->__('Priority'),
                'DOM.RP' => Mage::helper('chcanpost2module')->__('Regular Parcel'),
                'DOM.XP' => Mage::helper('chcanpost2module')->__('Xpresspost'),
                'DOM.XP.CERT' => Mage::helper('chcanpost2module')->__('Xpresspost Certified'),
                //usa
                'USA.EP' => Mage::helper('chcanpost2module')->__('Expedited Parcel USA'),
                'USA.PW.ENV' => Mage::helper('chcanpost2module')->__('Priority Worldwide Envelope USA'),
                'USA.PW.PAK' => Mage::helper('chcanpost2module')->__('Priority Worldwide pak USA'),
                'USA.PW.PARCEL' => Mage::helper('chcanpost2module')->__('Priority Worldwide Parcel USA'),
                'USA.SP.AIR' => Mage::helper('chcanpost2module')->__('Small Packet USA Air'),
                'USA.TP' => Mage::helper('chcanpost2module')->__('Tracked Packet - USA'),
                'USA.TP.LVM' => Mage::helper('chcanpost2module')->__('Tracked Packet – USA (LVM)'),
                'USA.XP' => Mage::helper('chcanpost2module')->__('Xpresspost USA'),
                //international
                'INT.IP.AIR' => Mage::helper('chcanpost2module')->__('International Parcel Air'),
                'INT.IP.SURF' => Mage::helper('chcanpost2module')->__('International Parcel Surface'),
                'INT.PW.ENV' => Mage::helper('chcanpost2module')->__('Priority Worldwide Envelope Int\’l'),
                'INT.PW.PAK' => Mage::helper('chcanpost2module')->__('Priority Worldwide pak Int\’l'),
                'INT.PW.PARCEL' => Mage::helper('chcanpost2module')->__('Priority Worldwide parcel Int\’l'),
                'INT.SP.AIR' => Mage::helper('chcanpost2module')->__('Small Packet International Air'),
                'INT.SP.SURF' => Mage::helper('chcanpost2module')->__('Small Packet International Surface'),
                'INT.TP' => Mage::helper('chcanpost2module')->__('Tracked Packet – International'),
                'INT.XP' => Mage::helper('chcanpost2module')->__('Xpresspost International'),
            ),

        );

        if (!isset($codes[$type])) {
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    /**
     * Gets whether or not tracking is available.
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Gets all Canada Post rates available for this order.
     *
     * @param array $data An assoc. array representing the API request payload.
     *
     * @return array
     */
    private function getCpRates($data, $request)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        } else {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
        }

        if ($request->getDestCountryId() != 'CA') {
            foreach ($request->getAllItems() as $item) {
                if ($item->getWeight() == 0) {
                    $this->fail_reason = 'weight';
                    Mage::getSingleton('checkout/session')->setServiceCode('');
                    return array();
                }
            }
        }

        $data = Mage::getModel('chcanpost2module/quote_param')->getParamsByQuote($quote->getId(), $data);

        if (Mage::getStoreConfig('carriers/chcanpost2module/require_coverage') == 'always'
            || Mage::getStoreConfig('carriers/chcanpost2module/require_coverage') == 'never'
        ) {

            $data['coverage'] = 0;

        }

        $service_code = Mage::getSingleton('checkout/session')->getServiceCode();

	// We have to request all rates all times
        if (0 && !empty($service_code)) {

            $data['services'] = array($service_code);

        }

        $rates = Mage::helper('chcanpost2module/rest_getRates')->getRates($data);

        $free_methods = explode(',', Mage::getStoreConfig('carriers/chcanpost2module/free_method'));

        if (empty($rates)) {

            $data['weight'] = 0.01;

            $rates = Mage::helper('chcanpost2module/rest_getRates')->getRates($data);

            if (!empty($rates)) {

                $this->fail_reason = 'weight';

                $rates = array();

            }

        } else if (!empty($service_code) && !empty($rates[$service_code]['expected-delivery-date'])) {
            if (in_array($service_code, $free_methods)
                && (float) $request->getPackageValue() >= (float) $this->getConfigData('free_shipping_subtotal')
            ) {

                $rates[$service_code]['price'] = '0.00';

            } else if ($this->getConfigData('flat_rate_for_canada_enable')
                && $request->getDestCountryId() == 'CA'
                && $rates[$service_code]['code'] != 'failure'
            ) {
                $rates[$service_code]['price'] = $this->getConfigData('flat_rate_for_canada');
            }

            if ($quote->getId()) {
                Mage::getModel('chcanpost2module/quote_param')->updateEstDeliveryDate($quote->getId(), $rates[$service_code]['expected-delivery-date']);
            }

        }

        // TODO 2015-03-12 find right coverage amount.
        $coverageValue = $quote->getGrandTotal();
        if(!$coverageValue && $request->getPackageValue()) {
            $coverageValue = $request->getPackageValue();
        }

        if(!$coverageValue && $quote->getSubtotal()) {
            $coverageValue = $quote->getSubtotal();
        }


        if (!empty($rates)
            && is_array($rates)
            && $coverageValue > Mage::getStoreConfig('carriers/chcanpost2module/coverage_treshhold')
            && Mage::getStoreConfig('carriers/chcanpost2module/require_coverage') == 'always') {

            $data['coverage'] = 1;

            $country_code = $request->getDestCountryId() ? $request->getDestCountryId() : 'CA';

            // sharper - force signature...
            if($coverageValue > Mage::getStoreConfig('carriers/chcanpost2module/signature_threshhold')
                && Mage::getStoreConfig('carriers/chcanpost2module/require_signature')) {
                $data['signature'] = 1;
            }

            foreach ($rates as $key => &$rate) {

                $max_coverage = Mage::helper('chcanpost2module/option')->getMaxCoverage($rate['code'], $country_code);

                if (empty($max_coverage)) {
                    // Service rate does not support coverage, so remove it from available options.
                    unset($rates[$key]);
                    continue;
                }

                $data['coverage_amount'] = min($coverageValue, $max_coverage);

                $data['services'] = array($rate['code']);

                $updatedRates = Mage::helper('chcanpost2module/rest_getRates')->getRates($data);

                $updatedRate = reset($updatedRates);  // Get the first rate, since there should be only one.

                if ($updatedRate['code'] != 'failure' && !empty($updatedRate['price'])) {
                    //expect to see only one rate for one service
                    $rate['price'] = $updatedRate['price'];
                } else {
                    // Service rate does not support coverage || SIGNATURE, so remove it from available options.
                    unset($rates[$key]);
                    continue;
                }

                if (!empty($service_code) && in_array($service_code, $free_methods)
                    && (float) $request->getPackageValue() >= (float) $this->getConfigData('free_shipping_subtotal')
                ) {

                    $rate['price'] = '0.00';

                } else if ($this->getConfigData('flat_rate_for_canada_enable')
                    && $request->getDestCountryId() == 'CA'
                    && $rate['code'] != 'failure'
                ) {
                    $rate['price'] = $this->getConfigData('flat_rate_for_canada');
                }

            }

        }

        // TODO what happens if they are in the admin?
        Mage::getSingleton('checkout/session')->setServiceCode('');

        return $rates;

    }

    /**
     * getIntersectRates
     *
     * @param array                            $packages An array of package data for shipping.
     * @param Mage_Shipping_Model_Rate_Request $request  The shipping rate request object.
     *
     * @return array
     */
    private function getIntersectRates($packages, $request)
    {

        $pack_rates = array();

        if (!empty($packages)) {

            foreach ($packages as $i => $pack) {

                $pack_rates[] = $this->getPackRates($pack, $request);

            }

        }

        $rates = array();

        $rates_counter = array();

        foreach ($pack_rates as $set_rates) {

            foreach ($set_rates as $rate) {

                $rates_counter[$rate['code']] = (!empty($rates_counter[$rate['code']])) ? $rates_counter[$rate['code']]+1: 1;

            }

        }


        foreach ($pack_rates as $k => $set_rates) {

            if(!count($set_rates) || isset($set_rates['failure']) || isset($set_rates['error'])) {
                if(isset($set_rates['failure']) || isset($set_rates['error'])) {
                    return $set_rates;
                }

                return false;
            }

            foreach ($set_rates as $rate) {

                // TODO I am not sure what this does
                // if (empty($rates[$rate['code']]) && $rates_counter[$rate['code']] == count($pack_rates)) {
                if (empty($rates[$rate['code']])) {

                    $rates[$rate['code']] = $rate;

                } else if (!empty($rates[$rate['code']])) {

                    $rates[$rate['code']]['price'] += $rate['price'];

                }

            }

        }

        return $rates;

    }

    /**
     * Makes an API request to get shipping rates for the given packages.
     *
     * @param array                            $pack    An assoc. array representing the package to send.
     * @param Mage_Shipping_Model_Rate_Request $request The shipping rate request object.
     *
     * @return array
     */
    private function getPackRates($pack, $request)
    {

        $weight = (!empty($pack['box'])) ? $pack['box']['weight'] : 0;

        if (!$weight && !empty($pack['items'])) {

            foreach ($pack['items'] as $item) {

                $weight += $item['weight'];

            }

        }

        $data = array(
            'xmlns' => 'http://www.canadapost.ca/ws/ship/rate',
            'weight' => (!empty($pack['box']['weight'])) ? $pack['box']['weight'] : $weight,
            'postal-code' => $request->getDestPostcode(),
            'country_code' => $request->getDestCountryId(),
            'box' => (!empty($pack['box'])) ? $pack['box'] : array(),
        );

        return $this->getCpRates($data, $request);

    }


    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {

        $allowed = explode(',', $this->getConfigData('allowed_methods'));

        $arr = array();

        foreach ($allowed as $k) {

            $arr[$k] = $this->getCode('method', $k);

        }

        return $arr;

    }


    /**
     * Calculate price considering free shipping and handling fee
     *
     * @param string $cost   The base rate for this shipping method.
     * @param string $method The shipping method type.
     *
     * @return string
     */
    public function getMethodPrice($cost, $method='')
    {
        $price = $this->getFinalPriceWithHandlingFee($cost);
        return $price;

    }

}
