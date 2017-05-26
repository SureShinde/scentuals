<?php

/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Data extends Mage_Core_Helper_Abstract {

    const DEFAULT_SIZE = 10;
    const ITEM_ID_SPLIT = '^|^';
    const LOG_FILE = 'canadapost_api.log';

    function getActiveQuote()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        } else {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            if ($quote->getId()) {
                $quote = Mage::getModel('sales/quote')->load($quote->getId());
            }
        }
        return $quote;
    }

    function isTableRatesCustomerGroup()
    {
        if($this->useTableRates()) {
            $tableRatesGroups = explode(',', Mage::getStoreConfig('carriers/chcanpost2module/tablerates_customergroup'));

            $customerGroupId = $this->getActiveQuote()->getCustomerGroupId();
            return  in_array($customerGroupId, $tableRatesGroups);
        }
        return false;
    }

    function useTableRates()
    {
        return Mage::helper('core')->isModuleEnabled('Collinsharper_Tablerate') &&
        Mage::getStoreConfigFlag('carriers/chcanpost2module/tablerates');
    }

    function updatePackingJsonObject($json)
    {
        $json = json_decode($json);
        $urlParams = array();

        if(Mage::registry('current_shipment')) {
            $urlParams['shipment_id'] = Mage::registry('current_shipment')->getId();
        }

        if(Mage::app()->getRequest()->getParam('order_id')) {
            $urlParams['order_id'] = Mage::app()->getRequest()->getParam('order_id');
        }

        $json['createLabelUrl'] = Mage::getUrl('*/sales_order_shipment/saveCpAjax', $urlParams);
        return $json;
    }


    function getLocale($rtype = 2)
    {

        $return = 'en';

        if (Mage::getStoreConfig('carriers/chcanpost2module/locale')) {

            if (stristr(Mage::app()->getLocale()->getDefaultLocale(), 'fr')) {

                $return = 'fr';

            }

        }

        if ($rtype != 2) {

            if ($return == 'fr') {

                $return = 'FR';

            }

            if ($return == 'en') {

                $return = 'EN';

            }

        }

        return $return;

    }

    public function log($message, $label=null) {

        if (Mage::getStoreConfig('carriers/chcanpost2module/debug')) {

            $backtrace = debug_backtrace();

            if ($label) {
                $message = "{$label}: {$message}";
            }

            Mage::log($backtrace[0]['file'] . '(line ' . $backtrace[0]['line'] . ') >>> ' . $message, null, self::LOG_FILE);
        }
    }



    public function getNearestOffices($postal_code)
    {

        $response = Mage::helper('chcanpost2module/rest_office')->getNearest($postal_code);

        $options = array();

        if (!empty($response)) {

            $xml = new SimpleXMLElement($response);

            foreach ($xml->{'post-office'} as $office) {

                $cp_office = Mage::getModel('chcanpost2module/office')->getByCpOfficeId($office->{'office-id'});

                if (!$cp_office->getId() || $cp_office->getLink() != (string)$office->link['href']) {

                    $cp_office->setCity((string)$office->address->city)
                        ->setPostalCode((string)$office->address->{'postal-code'})
                        ->setProvince((string)$office->address->province)
                        ->setAddress((string)$office->address->{'office-address'})
                        ->setLocation((string)$office->location)
                        ->setLink((string)$office->link['href'])
                        ->setMediaType((string)$office->link['media-type'])
                        ->setCpOfficeId((string)$office->{'office-id'})
                        ->setCpOfficeName((string)$office->name)
                        ->setBilingual((string)$office->{'bilingual-designation'})
                        ->setCraetedAt(date('Y-m-d H:i:s'))
                        ->save();

                }

                $address = $cp_office->getAddress();

                $address = str_replace('"', '', $address);

                $address = preg_replace('/^([a-z0-9]+\s*-\s*)/i', '', $address);

                $options[$cp_office->getId()] = array(
                    'id' => $cp_office->getId(),
                    'name' => $cp_office->getCpOfficeName(),
                    'gm_address' => $address.', '.$cp_office->getCity().', '.$cp_office->getProvince().', Canada',
                    'address' => $cp_office->getAddress().', '.$cp_office->getCity().', '.$cp_office->getProvince().', '.$cp_office->getPostalCode(),
                );

            }

        }

        return $options;

    }

    public function _getDefaultCurrencyFromCountry($country)
    {
        $currencies = array('AF' => 'AFA', 'AL' => 'ALL', 'DZ' => 'DZD', 'AS' => 'USD', 'AD' => 'EUR', 'AO' => 'AOA', 'AI' => 'XCD', 'AQ' => 'NOK', 'AG' => 'XCD', 'AR' => 'ARA', 'AM' => 'AMD', 'AW' => 'AWG', 'AU' => 'AUD', 'AT' => 'EUR', 'AZ' => 'AZM', 'BS' => 'BSD', 'BH' => 'BHD', 'BD' => 'BDT', 'BB' => 'BBD', 'BY' => 'BYR', 'BE' => 'EUR', 'BZ' => 'BZD', 'BJ' => 'XAF', 'BM' => 'BMD', 'BT' => 'BTN', 'BO' => 'BOB', 'BA' => 'BAM', 'BW' => 'BWP', 'BV' => 'NOK', 'BR' => 'BRL', 'IO' => 'GBP', 'BN' => 'BND', 'BG' => 'BGN', 'BF' => 'XAF', 'BI' => 'BIF', 'KH' => 'KHR', 'CM' => 'XAF', 'CA' => 'CAD', 'CV' => 'CVE', 'KY' => 'KYD', 'CF' => 'XAF', 'TD' => 'XAF', 'CL' => 'CLF', 'CN' => 'CNY', 'CX' => 'AUD', 'CC' => 'AUD', 'CO' => 'COP', 'KM' => 'KMF', 'CD' => 'CDZ', 'CG' => 'XAF', 'CK' => 'NZD', 'CR' => 'CRC', 'HR' => 'HRK', 'CU' => 'CUP', 'CY' => 'EUR', 'CZ' => 'CZK', 'DK' => 'DKK', 'DJ' => 'DJF', 'DM' => 'XCD', 'DO' => 'DOP', 'TP' => 'TPE', 'EC' => 'USD', 'EG' => 'EGP', 'SV' => 'USD', 'GQ' => 'XAF', 'ER' => 'ERN', 'EE' => 'EEK', 'ET' => 'ETB', 'FK' => 'FKP', 'FO' => 'DKK', 'FJ' => 'FJD', 'FI' => 'EUR', 'FR' => 'EUR', 'FX' => 'EUR', 'GF' => 'EUR', 'PF' => 'XPF', 'TF' => 'EUR', 'GA' => 'XAF', 'GM' => 'GMD', 'GE' => 'GEL', 'DE' => 'EUR', 'GH' => 'GHC', 'GI' => 'GIP', 'GR' => 'EUR', 'GL' => 'DKK', 'GD' => 'XCD', 'GP' => 'EUR', 'GU' => 'USD', 'GT' => 'GTQ', 'GN' => 'GNS', 'GW' => 'GWP', 'GY' => 'GYD', 'HT' => 'HTG', 'HM' => 'AUD', 'VA' => 'EUR', 'HN' => 'HNL', 'HK' => 'HKD', 'HU' => 'HUF', 'IS' => 'ISK', 'IN' => 'INR', 'ID' => 'IDR', 'IR' => 'IRR', 'IQ' => 'IQD', 'IE' => 'EUR', 'IL' => 'ILS', 'IT' => 'EUR', 'CI' => 'XAF', 'JM' => 'JMD', 'JP' => 'JPY', 'JO' => 'JOD', 'KZ' => 'KZT', 'KE' => 'KES', 'KI' => 'AUD', 'KP' => 'KPW', 'KR' => 'KRW', 'KW' => 'KWD', 'KG' => 'KGS', 'LA' => 'LAK', 'LV' => 'LVL', 'LB' => 'LBP', 'LS' => 'LSL', 'LR' => 'LRD', 'LY' => 'LYD', 'LI' => 'CHF', 'LT' => 'LTL', 'LU' => 'EUR', 'MO' => 'MOP', 'MK' => 'MKD', 'MG' => 'MGF', 'MW' => 'MWK', 'MY' => 'MYR', 'MV' => 'MVR', 'ML' => 'XAF', 'MT' => 'EUR', 'MH' => 'USD', 'MQ' => 'EUR', 'MR' => 'MRO', 'MU' => 'MUR', 'YT' => 'EUR', 'MX' => 'MXN', 'FM' => 'USD', 'MD' => 'MDL', 'MC' => 'EUR', 'MN' => 'MNT', 'MS' => 'XCD', 'MA' => 'MAD', 'MZ' => 'MZM', 'MM' => 'MMK', 'NA' => 'NAD', 'NR' => 'AUD', 'NP' => 'NPR', 'NL' => 'EUR', 'AN' => 'ANG', 'NC' => 'XPF', 'NZ' => 'NZD', 'NI' => 'NIC', 'NE' => 'XOF', 'NG' => 'NGN', 'NU' => 'NZD', 'NF' => 'AUD', 'MP' => 'USD', 'NO' => 'NOK', 'OM' => 'OMR', 'PK' => 'PKR', 'PW' => 'USD', 'PA' => 'PAB', 'PG' => 'PGK', 'PY' => 'PYG', 'PE' => 'PEI', 'PH' => 'PHP', 'PN' => 'NZD', 'PL' => 'PLN', 'PT' => 'EUR', 'PR' => 'USD', 'QA' => 'QAR', 'RE' => 'EUR', 'RO' => 'ROL', 'RU' => 'RUB', 'RW' => 'RWF', 'KN' => 'XCD', 'LC' => 'XCD', 'VC' => 'XCD', 'WS' => 'WST', 'SM' => 'EUR', 'ST' => 'STD', 'SA' => 'SAR', 'SN' => 'XOF', 'CS' => 'EUR', 'SC' => 'SCR', 'SL' => 'SLL', 'SG' => 'SGD', 'SK' => 'EUR', 'SI' => 'EUR', 'SB' => 'SBD', 'SO' => 'SOS', 'ZA' => 'ZAR', 'GS' => 'GBP', 'ES' => 'EUR', 'LK' => 'LKR',
            'SH' => 'SHP', 'PM' => 'EUR', 'SD' => 'SDG', 'SR' => 'SRG', 'SJ' => 'NOK', 'SZ' => 'SZL', 'SE' => 'SEK', 'CH' => 'CHF', 'SY' => 'SYP', 'TW' => 'TWD', 'TJ' => 'TJR', 'TZ' => 'TZS', 'TH' => 'THB', 'TG' => 'XAF', 'TK' => 'NZD', 'TO' => 'TOP', 'TT' => 'TTD', 'TN' => 'TND', 'TR' => 'TRY', 'TM' => 'TMM', 'TC' => 'USD', 'TV' => 'AUD', 'UG' => 'UGS', 'UA' => 'UAH', 'SU' => 'SUR', 'AE' => 'AED', 'GB' => 'GBP', 'US' => 'USD', 'UM' => 'USD', 'UY' => 'UYU', 'UZ' => 'UZS', 'VU' => 'VUV', 'VE' => 'VEF', 'VN' => 'VND', 'VG' => 'USD', 'VI' => 'USD', 'WF' => 'XPF', 'XO' => 'XOF', 'EH' => 'MAD', 'ZM' => 'ZMK', 'ZW' => 'USD'
        );
        if(isset($currencies[$country]))
        {
            return $currencies[$country];
        }
        return 'USD';
    }


    public function _getConversionRate($from, $to)
    {
        $session = Mage::getSingleton('adminhtml/session');
        $key = "_rate_{$from}_{$to}";

        $conversion = 1;
        $found = false;
        $allowedCurrencies = Mage::getModel('directory/currency')
            ->getConfigAllowCurrencies();
        if(in_array($to, $allowedCurrencies) && in_array($from, $allowedCurrencies))
        {
            try {
                $conversion = Mage::helper('directory')->currencyConvert(1, $from, $to);
                $session->setData($key, $conversion);
                $found = true;
            } catch (exception $e)
            {
                $conversion = 1;
            }
        }

        if(!$found)
        {
            if($session->getData($key))
            {
                $conversion = $session->getData($key);
            }
            else
            {
                $x = Mage::getModel('chcanpost2module/currency_import_webservicex');
                $rate = $x->chGetRate($from, $to);
                if($rate)
                {
                    $session->setData($key, $rate);
                    $conversion = $rate;
                }
            }
        }

        return $conversion;
    }


    public function getBoxForItems($items, $qtys = array(), $forceQty = false)
    {
        $boxes = array();
        try {

            if (Mage::helper('core')->isModuleEnabled('Collinsharper_ShippingBox') &&
                Mage::helper('chbox')->isActive()) {
                $this->log(__LINE__ . " " . __FUNCTION__ . " BOX PACKING");
                // TODO: webshopapps multi box is not setup to work with this.
                $boxes = Mage::helper('chbox')->selectBoxForItems($items, $qtys);

            }

        } catch (Exception $e) {
            $this->log(__LINE__ . " " . __FUNCTION__ . " BOX PACKING fail ");
            mage::log(__METHOD__ . " Exception in getting boxes form Collinsharper.com " . $e->getMessage());
            Mage::logException($e);
            $boxes = array();

        }


        if(!count($boxes)) {

            $boxes[0]['box']['l'] = $this->_getConvertDim(Mage::getStoreConfig('carriers/chcanpost2module/default_l'));
            $boxes[0]['box']['w'] = $this->_getConvertDim(Mage::getStoreConfig('carriers/chcanpost2module/default_w'));
            $boxes[0]['box']['h'] = $this->_getConvertDim(Mage::getStoreConfig('carriers/chcanpost2module/default_h'));

            $boxes[0]['box']['weight'] = 0;
            $readyToShipBoxes = array();

            $formatItems = array();
            $k = 0;
            foreach ($items as $i => $item) {
                // we need to skip is virtual ior it has parent do somethign different?
                $itemId = $this->_getItemId($item);


                $this->log(__METHOD__ . " we have " . print_r($qtys,1));

                // on create shipment - we dont always use all items..
                if($forceQty && (empty($qtys[$itemId]) || $qtys[$itemId] == 0)) {
                    continue;
                }


                $originalReadyToShip = $readyToShipBoxes;
                $product_id = $item->getProductId();
                $product = Mage::getModel('catalog/product')->load($product_id);

                $qty = (!empty($qtys[$itemId])) ? $qtys[$itemId] : $item->getQty();

                $this->log(__METHOD__ . " we have Q " . $itemId . " aaa " . print_r($qty,1));


                if (Mage::helper('core')->isModuleEnabled('Webshopapps_Shipusa')
                    //&& ($product->getData('split_product'))
                ) {

                    try {
                        $this->log(__LINE__ . " " . __FUNCTION__ . " WEB SHO BOXING ");

                        $wsaBoxes = Mage::getModel('shipusa/shipboxes')->getCollection()
                            ->addProductFilter($product->getId());

                        if($wsaBoxes) {
                            $tmpbox = array();
                            foreach($wsaBoxes as $b) {
                                $tmpbox[] = $b;
                            }
                            $wsaBoxes = $tmpbox;

                            unset($tmpbox);

                        }

                        // WSA has 2 different boxing methods.
                        $singleDataIn = Mage::getModel('shipusa/singleboxes')->getCollection()
                            ->addProductFilter($product->getId());


                        if(!$wsaBoxes || !$wsaBoxes->count() && $singleDataIn && $singleDataIn->count()) {

                            $this->log(__METHOD__ . __LINE__ . " single box");
                            $wsaBoxes = array();
                            foreach($singleDataIn as $singeBox) {
                                if($singeBox->getData('length') == -1 && $singeBox->getData('box_id')) {
                                    $singeBox = Mage::getModel('boxmenu/boxmenu')->load($singeBox->getData('box_id'));
                                    if($singeBox->getLength()) {
                                        $wsaBoxes[] = $singeBox;
                                    }
                                }
                            }
                        }

                           $wsaBoxes = (array)$wsaBoxes;
                        $testOne = $wsaBoxes && count((array)$wsaBoxes) > 0;
                        //$testTwo = is_object($wsaBoxes) && $wsaBoxes->getFIrstItem()->getData('length');
                        $testThree = is_array($wsaBoxes) && $wsaBoxes[0]->getData('length');
                        if ($testOne && $testThree) { 

                            for ($iQty = 0; $iQty < $qty; $iQty++) {

                                foreach ($wsaBoxes as $multiBox) {


                                    $box = array();
                                    $formatItems = array();

                                    // TODO We expect WSA to handle weight..
                                    $thisboxWeight = Mage::helper('chunit')->getConvertedWeight($multiBox->getData('weight') == 0 ? $item->getWeight() : $multiBox->getData('weight'), $this->getStoreConfig('catalog/measure_units/weight'), 'kg');
                                    $box['box']['l'] = Mage::helper('chunit')->getConvertedLength($multiBox->getData('length'), $this->getStoreConfig('catalog/measure_units/length'), 'cm');
                                    $box['box']['w'] = Mage::helper('chunit')->getConvertedLength($multiBox->getData('width'), $this->getStoreConfig('catalog/measure_units/length'), 'cm');
                                    $box['box']['h'] = Mage::helper('chunit')->getConvertedLength($multiBox->getData('height'), $this->getStoreConfig('catalog/measure_units/length'), 'cm');
                                    $box['box']['weight'] = round($thisboxWeight,2);
                                    //$box['box']['weight'] = $this->getConvertedWeight($multiBox->getData('weight') == 0 ? $item->getWeight() : $multiBox->getData('weight'));

                                    $formatItems[$this->_getItemId($item)] = $this->_prepareItemForBox($item, $product);


//                                    if($formatItems[0]['weight'] > $thisboxWeight) {
//                                        $formatItems[0]['weight'] = $thisboxWeight;
//                                    }

                                    $box['items'] = $formatItems;

                                    $eachAdd = $multiBox->getData('num_boxes') ?  $multiBox->getData('num_boxes') : 1;
                                    for ($i = 0; $i < $eachAdd; $i++) {
                                        $readyToShipBoxes[] = $box;
                                    }

                                }
                            }
                            continue;
                        } else if ($product->getReadyToShip()) {
                            $readyToShipBoxes = $this->_prepareReadyToShip($item, $product, $qty, $readyToShipBoxes);
                            continue;
                        }
                        // no data we continue down below and let CP just pack the item?

                    } catch (Exception $e) {
                        // try to use the webshop apps - we fialed? just revert RTS packa the item and move on?
                        $this->log(__LINE__ . " " . __FUNCTION__ . " webshop fail BOXING ");
                        mage::logexception($e);
                        $readyToShipBoxes = $originalReadyToShip;
                        // no data we continue down below and let CP just pack the item?

                    }

                } else if($product->getReadyToShip()) {
                    // if the product is ready to ship we create boxes for it and add it to the stack later.
                    $this->log(__LINE__ . " " . __FUNCTION__ . " RTS BOXING ");

                    $readyToShipBoxes = $this->_prepareReadyToShip($item, $product, $qty, $readyToShipBoxes);
                    continue;
                }

                $this->log(__LINE__ . " " . __FUNCTION__ . " STD BOXING ");
                $this->log(__LINE__ . " " . __FUNCTION__ . "packing box with product  " . $product->getId() . "  with weight " . $product->getWeight());
                $this->log(__LINE__ . " " . __FUNCTION__ . "packing box with item " . $item->getProductId() . "  with weight " . $item->getWeight());
                $boxes[0]['box']['weight'] += $qty * $this->getConvertedWeight($product->getWeight() == 0 ? $item->getWeight() : $product->getWeight());

                if($boxes[0]['box']['weight'] <= 0.01) {
                    $boxes[0]['box']['weight'] = 0.01;
                }

                // must add each item to the box with a unique ID
                for($qtyCounter=0; $qtyCounter < $qty;$qtyCounter++) {
                    $formatItems[$this->_getItemId($item) . Collinsharper_Canpost_Helper_Data::ITEM_ID_SPLIT . $qtyCounter] = $this->_prepareItemForBox($item, $product);
                }

            }

            $boxes[0]['items'] = $formatItems;

            if(count($readyToShipBoxes)) {
                $boxes = array_merge($boxes, $readyToShipBoxes);
            }

            foreach($boxes as $k => $box) {
                if($box['box']['weight'] == 0) {
                    unset($boxes[$k]);
                }
                $box['box']['weight'] = round($box['box']['weight'],2) > 0 ? round($box['box']['weight'],2) : 0.01;

		// Check if the weight of items in the box is 0 or not. If it is 0, destroy the box.
		// The configurable product with "Ready to Ship" option creates an empty box for shipping, which cause doulbe shipping fee. The code above cannot get rid of this empty box because the box has it's own weight. The following code check the items' weight and get rid of the box with items with weight 0.
				
		$sumOfItemWeight = 0;
				
		foreach($box['items'] as $item){
			$sumOfItemWeight += $item['weight'];
		}
				
		if($sumOfItemWeight == 0){
			unset($boxes[$k]);
		}

            }
            reset($boxes);

        }

        $this->log(__LINE__ . " " . __FUNCTION__ . " shipping boxes " . print_r($boxes,1));

        return $boxes;

	}

    // TODO: make this store wise?
    public function getStoreConfig($path)
    {
        return Mage::getStoreConfig($path);
    }

    public function getConvertedProductDimension($product, $which, $measure = 'cm')
    {
        $field = $this->getStoreConfig('catalog/product_data/' . $which);
        $converted = $this->_getConvertDim($product->getData($field));
        return $converted > 0 ? $converted : self::DEFAULT_SIZE;
    }

    public function _getConvertDim($x, $measure = 'cm')
    {
        return Mage::helper('chunit')->getConvertedLength($x, $this->getStoreConfig('catalog/measure_units/length'), $measure);
    }

    public function getConvertedWeight($weight, $measure = 'kg')
    {
        return Mage::helper('chunit')->getConvertedWeight($weight, Mage::getStoreConfig('catalog/measure_units/weight'), $measure);
    }

    public function _getItemId($item)
    {
        $itemId = $item->getOrderItemId();
        if($item->getParentItemId()) {
            $itemId = $item->getParentItemId();
        }

        return $itemId ? $itemId : $item->getItemId();
    }

    public function _prepareItemForBox($item, $product, $itemId = false)
    {
        if(!$itemId) {
            $itemId = $this->_getItemId($item);
        }

        $returnItem = array(
            'id' => $itemId,
            'l' => $this->getConvertedProductDimension($product, 'product_length'),
            'w' => $this->getConvertedProductDimension($product, 'product_width'),
            'h' => $this->getConvertedProductDimension($product, 'product_height'),
            'weight' => $this->getConvertedWeight($product->getWeight()),
        );

        if (empty($returnItem['w'])) {
            $returnItem['w'] = self::DEFAULT_SIZE;
        }

        if (empty($returnItem['l'])) {
            $returnItem['l'] = self::DEFAULT_SIZE;
        }

        if (empty($returnItem['h'])) {
            $returnItem['h'] = self::DEFAULT_SIZE;
        }
        return $returnItem;

    }


    public function _prepareReadyToShip($item, $product, $qty, $readyToShipBoxes)
    {
        if(!$qty || $qty < 1) {
            $qty = 1;
        }

        $box = array();
        $formatItems = array();
        $itemId = $this->_getItemId($item);

        $box['box']['l'] = $this->getConvertedProductDimension($product, 'product_length');
        $box['box']['w'] = $this->getConvertedProductDimension($product, 'product_width');
        $box['box']['h'] = $this->getConvertedProductDimension($product, 'product_height');
        $box['box']['weight'] = $this->getConvertedWeight($product->getWeight() == 0 ? $item->getWeight() : $product->getWeight());
        $box['box']['weight'] = round($box['box']['weight'], 2);

        if($box['box']['weight'] <= 0) {
            $box['box']['weight'] = 0.01;
        }

        $formatItems[$itemId] = $this->_prepareItemForBox($item, $product);
        $box['items'] = $formatItems;

        $this->log(__METHOD__ . __LINE__ . " we RTP PROD " . print_r($box,1));



        for($i=0;$i<$qty;$i++) {
            $readyToShipBoxes[] = $box;
        }

        return $readyToShipBoxes;
    }


    public function formatDate($date)
    {
        $format = Mage::getStoreConfig('carriers/chcanpost2module/date_format');

        switch ($format) {
            case 'full':
                return date('l, F j, Y', strtotime($date));
            case 'long':
                return date('F j, Y', strtotime($date));
            case 'medium':
                return date('M j, Y', strtotime($date));
            case 'short':
                return date('n/j/y', strtotime($date));
        }

        return $date;
    }


    public function getNotifyOnShipment($forXml=false)
    {
        $notifyOnShipment = Mage::getStoreConfig('carriers/chcanpost2module/notify_on_shipment');

        if ($forXml) {
            return ($notifyOnShipment ? 'true' : 'false');
        }

        return $notifyOnShipment;
    }


    public function getNotifyOnException($forXml=false)
    {
        $notifyOnException = Mage::getStoreConfig('carriers/chcanpost2module/notify_on_exception');

        if ($forXml) {
            return ($notifyOnException ? 'true' : 'false');
        }

        return $notifyOnException;
    }


    public function getNotifyOnDelivery($forXml=false)
    {
        $notifyOnDelivery = Mage::getStoreConfig('carriers/chcanpost2module/notify_on_delivery');

        if ($forXml) {
            return ($notifyOnDelivery ? 'true' : 'false');
        }

        return $notifyOnDelivery;
    }

    public function validateCallback($response = array())
    {
        if (isset($response['valid']) && $response['valid'] == true) {
            return true;
        }

        return false;
    }

}
