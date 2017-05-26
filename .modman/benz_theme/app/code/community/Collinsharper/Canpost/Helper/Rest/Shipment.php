<?php
/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Shipment extends Collinsharper_Canpost_Helper_Rest_Request {

    CONST MAX_ADDRESS_LINE_LENGTH = 44;

    /**
     * @param $shipping_address
     * @param $quote
     * @param $group_id
     * @param $params
     * @return string
     */
    public function _createCustomsNode($shipping_address, $order, $group_id, $params)
    {

        $xml = '';

        if($shipping_address->getCountryId() != 'CA') {

            $item_xml = '';

            $parentProductTypes = array('grouped', 'configurable', 'bundle');
//            TODO allow for simple products to be shown on the Shipping labels
//            $useBundleItem = Mage::getStoreConfig('carriers/chcanpost2module/use_bundle') == true;
            $useBundleItem = true;
            //foreach ($quote->getAllItems() as $item) {
            //foreach ($params['_order']->getAllItems() as $item) {
            $shipment = Mage::getModel('sales/order_shipment')->load($params['current_shipment_id']);

            //foreach ($params['_order']->getAllItems() as $item) {
            foreach ($shipment->getAllItems() as $ship_item) {

                $item = $ship_item->getOrderItem();
                // TODO What is a dummy item?
                if($item->isDummy(true) || $item->getProduct()->getTypeId() == 'virtual') {
                    continue;
                }

                // if we have a parent and we only want the bundle .. we skip the small items.
                if($useBundleItem && $item->getParentItemId()) {
                    continue;
                }

                // if we have a parent TYPE and we only want the kids.. we skip it.
                if(!$useBundleItem && in_array($item->getProduct()->getTypeId(), $parentProductTypes)) {
                    continue;
                }


                $product = Mage::GetModel('catalog/product')->load($item->getProductId());
                // getQty
                // this was not allowing CFG products to be reviewed for addition to a shipment..
                //if ($product->getTypeId() != 'simple' || $item->getData('qty_shipped') == 0) {
                if ($item->getData('qty_shipped') == 0) {

                    continue;
                }

                $item_xml .= '
                <item>
                <customs-number-of-units>'.(int)$ship_item->getQty().'</customs-number-of-units>
                <customs-description><![CDATA['.substr($item->getName(),0,43).']]></customs-description>
                <sku>'.substr($item->getSku(),0,43).'</sku>
                ';

                $hs_tariff_code = $product->getHsTariffCode();

                if (!empty($hs_tariff_code)) {

                    $item_xml .= '<hs-tariff-code>'.$hs_tariff_code.'</hs-tariff-code>';

                }

                $custom_weight = $item->getWeight();

                if (Mage::helper('core')->isModuleEnabled('Collinsharper_MeasureUnit')) {

                    $custom_weight = round(Mage::helper('chunit')->getConvertedWeight(
                        $custom_weight,
                        Mage::getStoreConfig('catalog/measure_units/weight'),
                        'kg'
                    ),2);

                }

                $origin_country = $product->getData('country_of_manufacture');
                //  $origin_country  = 'Canada';
                //  $origin_country  = 'United States';
                //  $origin_country  = 'Italy';
                if(strlen($origin_country) > 2) {
                    // TODO go load country by name and get the 2 letter code

                    $countryCollection = Mage::getModel('directory/country')->getCollection();
                    foreach ($countryCollection as $country) {
                        if ($origin_country == $country->getName()) {
                            $origin_country = $country->getCountryId();
                            break;
                        }
                    }
                }
                $origin_province = $product->getAttributeText('origin_province');

                $item_xml .= '
                <unit-weight>'. (round($custom_weight,2) > 0.01 ? round($custom_weight,2) : 0.01) .'</unit-weight>';

                $item_xml .= '
                <customs-value-per-unit>'.round($item->getPrice(),2).'</customs-value-per-unit>';

                if (!empty($origin_country)) {
                    if ($origin_country != 'CA') {
                        $item_xml .= '<country-of-origin>'.$origin_country.'</country-of-origin>';
                    } else if ($origin_country == 'CA' && !empty($origin_province)) {
                        $item_xml .= '<country-of-origin>'.$origin_country.'</country-of-origin>';

                        $province_code = Mage::getModel('directory/region')
                            ->getCollection()
                            ->addFieldToFilter('default_name', $origin_province)
                            ->getFirstItem()
                            ->getCode();

                        $item_xml .= '<province-of-origin>'.$province_code.'</province-of-origin>';
                    }
                }

                $item_xml .= '</item>
                ';
            }
            $item_xml = '
            <sku-list>
            ' . $item_xml . '
            </sku-list>
            ';

            $helper = Mage::helper('chcanpost2module');

            $base_currency = $order->getBaseCurrencyCode();

            $customs_value = $order->getBaseSubtotal();

            $currency_conversion = 1;

            $destination_currency = $helper->_getDefaultCurrencyFromCountry($shipping_address->getCountryId());

            $currency_conversion = $helper->_getConversionRate($base_currency, $destination_currency);

            $converted_value = number_format($currency_conversion*$customs_value,2);

            $reason_for_export = Mage::getStoreConfig('carriers/chcanpost2module/reason_for_export');

            $xml .= '
            <customs>
                <currency>'.$destination_currency.'</currency>
                <conversion-from-cad>'.$currency_conversion.'</conversion-from-cad>
                <reason-for-export>'.$reason_for_export.'</reason-for-export>';
            if ($reason_for_export == 'OTH') {
                $xml .= '<other-reason>'.Mage::getStoreConfig('carriers/chcanpost2module/other_reason').'</other-reason>';
            }
            $xml .=
                $item_xml.'
            </customs>';

        }

        return $xml;

    }


    /**
     *
     * @param mage shipment_address $shipping_address
     * @param object mage quote $quote
     * @param int $group_id
     * @return type
     */
    public function create($shipping_address, $order, $group_id = 1, $params = array())
    {

        $xmlRequest = $this->composeXml($shipping_address, $order, $group_id, $params);

        if ($this->isContract()) {

            $url = $this->getBaseUrl().'rs/' . $this->getBehalfAccount() . '/' . Mage::getStoreConfig('carriers/chcanpost2module/api_customer_number') . '/shipment';

            $headers = array(
                'Content-Type: application/vnd.cpc.shipment-v2+xml',
                'Accept: application/vnd.cpc.shipment-v2+xml'
            );
        } else {

            $url = $this->getBaseUrl().'rs/' . $this->getBehalfAccount() . '/ncshipment';

            $headers = array(
                'Content-Type: application/vnd.cpc.ncshipment+xml',
                'Accept: application/vnd.cpc.ncshipment+xml'
            );
        }

        return $this->send($url, $xmlRequest, false, $headers);

    }


    /**
     *
     * @param string $cp_shipment_id
     */
    public function void($cp_shipment_id)
    {
        $shipmentLink = Mage::getResourceModel('chcanpost2module/link_collection')
            ->addFieldToFilter('cp_shipment_id', $cp_shipment_id)
            ->addFieldToFilter('rel', 'self')
            ->getFirstItem();
        if (!$shipmentLink->getId()) {
            throw new Exception("Shipment has already been deleted.");
        }

        $url = $shipmentLink->getUrl();

        $headers = array(
            'Accept:' . $shipmentLink->getMediaType()
        );

        return $this->send($url, null, false, $headers, 'DELETE');

    }


    /**
     *
     * @param type $shipping_address
     * @param type $quote
     * @param type $group_id
     * @param type $params
     * @return string
     */
    private function composeXml($shipping_address, $order, $group_id, $params)
    {

        $customs_node = $this->_createCustomsNode($shipping_address, $order, $group_id, $params);

        $customer_email = $shipping_address->getEmail();

        if (empty($customer_email)) {

            $customer_email = $order->getCustomerEmail();

        }

        if (!empty($params['cp_office_id'])) {

            $office = Mage::getModel('chcanpost2module/office')->load($params['cp_office_id']);

            $address = array(
                'city' => $office->getCity(),
                'province' => $office->getProvince(),
                'address' => $office->getAddress(),
                'country' => 'CA',
                'postal_code' => $this->formatPostalCode($office->getPostalCode()),
            );

            $destination_name = $office->getCpOfficeName();

        } else {

            $street = $shipping_address->getStreet();

            $address = array(
                'city' => $shipping_address->getCity(),
                'province' => $shipping_address->getRegionCode(),
                'address' => (is_array($street)) ? implode(', ', $street) : $street,
                'country' => $shipping_address->getCountryId(),
                'postal_code' => $this->formatPostalCode($shipping_address->getPostcode()),
            );

            $destination_name = $shipping_address->getCompany();

        }

        $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>';

        if ($this->isContract()) {

            $xmlRequest .= '<shipment xmlns="http://www.canadapost.ca/ws/shipment">
                                <group-id>'.$group_id.'</group-id>
                                <requested-shipping-point>'.$this->formatPostalCode(Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId())).'</requested-shipping-point>
                                <expected-mailing-date>'.date('Y-m-d').'</expected-mailing-date>';

        } else {

            $xmlRequest .= '<non-contract-shipment xmlns="http://www.canadapost.ca/ws/ncshipment">';


        }

        $lineTwo = '';
        if(Mage::getStoreConfig('shipping/origin/street_line2', $order->getStoreId())) {

            $lineTwo = '                              <address-line-2><![CDATA['.Mage::getStoreConfig('shipping/origin/street_line2', $order->getStoreId()).']]></address-line-2>';
        }


        $srcCountry = Mage::getStoreConfig('shipping/origin/country_id', $order->getStoreId() );
        $srcProvince = Mage::getModel('directory/region')->loadByName(Mage::getStoreConfig('shipping/origin/region_id', $order->getStoreId() ), $srcCountry)->getCode();
        if(is_numeric(Mage::getStoreConfig('shipping/origin/region_id', $order->getStoreId() )) && ! $srcProvince) {
            $srcProvince = Mage::getModel('directory/region')->load(Mage::getStoreConfig('shipping/origin/region_id', $order->getStoreId() ))->getCode();
        }


        $xmlRequest .= '
                <delivery-spec>
                        <service-code>'.(!empty($params['service_code']) ? $params['service_code'] : 'DOM.EP').'</service-code>
                        <sender>
                                <company><![CDATA['.Mage::getStoreConfig('general/store_information/name', $order->getStoreId()).']]></company>
                                <contact-phone>'.Mage::getStoreConfig('general/store_information/phone', $order->getStoreId()).'</contact-phone>
                                <address-details>
                                        <address-line-1>'.Mage::getStoreConfig('shipping/origin/street_line1', $order->getStoreId() ).'</address-line-1>
                                        ' . $lineTwo . '
                                        <city>'.Mage::getStoreConfig('shipping/origin/city',  $order->getStoreId() ).'</city>
                                        <prov-state>' . $srcProvince . '</prov-state>';

        if ($this->isContract()) {

            $xmlRequest .= '<country-code>' . $srcCountry . '</country-code>';

        }

        $xmlRequest .= '
                                        <postal-zip-code>'.$this->formatPostalCode(Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId() )).'</postal-zip-code>
                                </address-details>
                        </sender>
                        <destination>
                                <name><![CDATA['.$shipping_address->getFirstname().' '.$shipping_address->getLastname().']]></name>';

        if (!empty($destination_name)) {

            $xmlRequest .=     '<company><![CDATA['.$destination_name.']]></company>';

        }

        $xmlRequest .= '
                                <address-details>
                                        <address-line-1>'.$shipping_address->getStreet(1) .'</address-line-1>
										<address-line-2>'.$shipping_address->getStreet(2) .'</address-line-2>';

//mage::log(__METHOD__ . __LINE__ . " string " . (string)$shipping_address->getStreet(1) . " and " . strlen((string)$shipping_address->getStreet(1)));
//mage::log(__METHOD__ . __LINE__ . " string " . (string)$shipping_address->getStreet(2) . " and " . strlen((string)$shipping_address->getStreet(2)));

        if (strlen((string)$shipping_address->getStreet(1)) > self::MAX_ADDRESS_LINE_LENGTH || strlen((string)$shipping_address->getStreet(2)) > self::MAX_ADDRESS_LINE_LENGTH) {
            Mage::throwException("The address has more than 44 characters in the line, please edit the order and correct the address line");
        }

        // KL: We need to make sure the weight information that we pass onto CP is in correct format
        $cp_weight = 0.01;
        if (!empty($params['weight'])) {
            $cp_weight = (double)trim($params['weight']);
        }

        $xmlRequest .= '
                                        <city>'.$address['city'].'</city>
                                        <prov-state>'.(!empty($address['province']) ? $address['province'] : ' . ').'</prov-state>
                                        <country-code>'.$address['country'].'</country-code>
                                        <postal-zip-code>'.$this->formatPostalCode($address['postal_code']).'</postal-zip-code>
                                </address-details>
                                <client-voice-number>'.$shipping_address->getTelephone().'</client-voice-number>
                        </destination>
                        <options>'.(!empty($params['options']) ? $params['options'] : '').'</options>
                        <parcel-characteristics>
                                <weight>' . (round($cp_weight,2) > 0.01 ? round($cp_weight,2) : 0.01) . '</weight>';

        if (!empty($params['box'])) {

            $xmlRequest .= '
                                                <dimensions>
                                                        <length>'.$params['box']['l'].'</length>
                                                        <width>'.$params['box']['w'].'</width>
                                                        <height>'.$params['box']['h'].'</height>
                                                </dimensions>';

        } else {

            $xmlRequest .= '
                                                <dimensions>
                                                        <length>'.Mage::helper('chunit')->getConvertedLength(Mage::getStoreConfig('carriers/chcanpost2module/default_l'), Mage::getStoreConfig('catalog/measure_units/length'), 'cm').'</length>
                                                        <width>'.Mage::helper('chunit')->getConvertedLength(Mage::getStoreConfig('carriers/chcanpost2module/default_w'), Mage::getStoreConfig('catalog/measure_units/length'), 'cm').'</width>
                                                        <height>'.Mage::helper('chunit')->getConvertedLength(Mage::getStoreConfig('carriers/chcanpost2module/default_h'), Mage::getStoreConfig('catalog/measure_units/length'), 'cm').'</height>
                                                </dimensions>';

        }


        $notifyOnShipment = Mage::helper('chcanpost2module')->getNotifyOnShipment(true);
        $notifyOnException = Mage::helper('chcanpost2module')->getNotifyOnException(true);
        $notifyOnDelivery = Mage::helper('chcanpost2module')->getNotifyOnDelivery(true);

        //Mage::helper('chcanpost2module')->log(print_r($params, true), 'params');

        if (isset($params['options']) && strpos($params['options'], 'D2PO') !== false) {
            $notifyOnShipment = 'true';
            $notifyOnException = 'true';
            $notifyOnDelivery = 'true';
        }

        $xmlRequest .= '
                                <unpackaged>false</unpackaged>
                                <mailing-tube>false</mailing-tube>
                        </parcel-characteristics>
                        <notification>
                                <email>'.$customer_email.'</email>
                                <on-shipment>'  . $notifyOnShipment  .'</on-shipment>
                                <on-exception>' . $notifyOnException .'</on-exception>
                                <on-delivery>'  . $notifyOnDelivery  .'</on-delivery>
                        </notification>
                        <references>
                                <customer-ref-1>'.$params['_order']->getIncrementId().'</customer-ref-1>
                        </references>
                        '.$customs_node;

        if ($this->isContract()) {

            $xmlRequest .= '
                        <print-preferences>
	    ';
            if (Mage::getStoreConfig('carriers/chcanpost2module/output_format') == 'zpl') {
                $xmlRequest .= '<output-format>4x6</output-format>';

                if(strpos(Mage::helper('core/url')->getCurrentUrl(), 'admin/manifest/massCreate')  === false) {
                    $xmlRequest .= '<encoding>ZPL</encoding>';

                }

            } else {
                $xmlRequest .= '<output-format>'.Mage::getStoreConfig('carriers/chcanpost2module/output_format').'</output-format>';
            }
            $xmlRequest .= '
                        </print-preferences>';
        }

        $xmlRequest .= '
                        <preferences>
                                <show-packing-instructions>true</show-packing-instructions>
                                <show-postage-rate>false</show-postage-rate>
                                <show-insured-value>true</show-insured-value>
                        </preferences>';

        if ($this->isContract()) {

            $xmlRequest .= '<settlement-info>
                                <contract-id>'.Mage::getStoreConfig('carriers/chcanpost2module/contract').'</contract-id>
                                <intended-method-of-payment>'.(Mage::getStoreConfig('carriers/chcanpost2module/has_default_credit_card') ? 'CreditCard' : 'Account').'</intended-method-of-payment>
                            </settlement-info>';

        }

        $xmlRequest .= '</delivery-spec>';

        if ($this->isContract()) {

            $xmlRequest .= '</shipment>';

        } else {

            $xmlRequest .= '</non-contract-shipment>';

        }

        return $xmlRequest;

    }

}
