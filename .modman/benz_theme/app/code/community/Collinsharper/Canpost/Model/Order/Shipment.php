<?php
/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Order_Shipment extends Mage_Sales_Model_Order_Shipment {


	private $xml;

	private $error;


	/**
	 * on add shipment to manifest
	 *
	 * @param int $group_id
	 * @param int $manifest_id
	 * @param int $magento_shipment_id
	 * @return boolean
	 */
	public function createCpShipment($group_id, $manifest_id, $magento_shipment_id) {

		$shipping_address = Mage::getModel('sales/order_address')->load($this->getShippingAddressId());

		//I have no clue why this does not work
		//$quote = Mage::getModel('sales/quote')->load($this->getOrder()->getQuoteId());
		$quote = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('entity_id', $this->getOrder()->getQuoteId())->getFirstItem();

		$params = array('weight' => 0);

		$params['_order'] = $this->getOrder();

		$inner_boxes = array();

		$shipment = Mage::getModel('sales/order_shipment')->load($magento_shipment_id);




		// GET THE COUNTRY \
		// LOOP LOOPY PRODUT TARRIG CODES
		if($this->getOrder()->getShippingAddress()->getCountryId() != 'CA') {
			$missingHardCodes = false;
			foreach($shipment->getAllItems() as $item) {
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
				if(!$product->getHsTariffCode()) {
					throw new Exception(Mage::helper('chcanpost2module')->__('Canada Post requires all items in a foreign shipment to have valid HS Tariff codes. Please refer to The following article for assistance <a href="http://www.collinsharper.com/magentohelp/kb/faq.php?id=128" target="_blank">Canada Post HS Tariff Codes</a>'));
				}
			}

		}

		$packages = $shipment->getPackages();

		if (empty($packages)) {

			$pack = Mage::helper('chcanpost2module')->getBoxForItems($shipment->getAllItems());

		} else {

			$pack = array();

			foreach (unserialize($packages) as $i=>$box) {

				$pack[$i]['box'] = array(
					'weight' => round($box['params']['weight'],2),
					'w' => Mage::helper('chunit')->getConvertedLength($box['params']['width'], ($box['params']['dimension_units'] == 'INCH' ? 'inch' : 'cm'), 'cm'),
					'h' => Mage::helper('chunit')->getConvertedLength($box['params']['height'], ($box['params']['dimension_units'] == 'INCH' ? 'inch' : 'cm'), 'cm'),
					'l' => Mage::helper('chunit')->getConvertedLength($box['params']['length'], ($box['params']['dimension_units'] == 'INCH' ? 'inch' : 'cm'), 'cm'),
					'weight_units' => ($box['params']['weight_units'] == 'POUND' ? 'lb' : 'kg'),
					'dimension_units' => ($box['params']['dimension_units'] == 'INCH' ? 'inch' : 'cm'),
				);

				$pack[$i]['items'] = $box['items'];

			}

		}

		foreach ($pack as $box) {

			$params['box'] = $box['box'];

			$params['weight'] = 0;

			if (!empty($params['box']['weight'])) {

				$params['weight'] = (!empty($params['box']['weight_units']) ? Mage::helper('chunit')->getConvertedWeight($params['box']['weight'], $params['box']['weight_units'], 'kg') : $params['box']['weight']);

			}

			//else weight already included in box weight
			// 20131216 - this is legacy code that is replaced by the new return from getBoxForItems which includes box weight and item weight.
			// this will as well need to be tested with creating a manual package and running manuifest then
			if (0 && empty($packages)) {

				foreach($shipment->getAllItems() as $item) {

					if (!empty($box['items']) && empty($box['items'][$item->getOrderItemId()])) {

						continue;

					}

					$weight = Mage::helper('chunit')->getConvertedWeight($item->getWeight(), Mage::getStoreConfig('catalog/measure_units/weight'), 'kg');

					$params['weight'] += ($weight * $item->getQty()) ;

				}

			}

			$params['service_code'] = str_replace('chcanpost2module_', '', $this->getOrder()->getShippingMethod());

			if ($params['service_code'] == 'failure') {

				$data = array(
					'country_code' => $this->getOrder()->getShippingAddress()->getCountryId(),
					'postal-code'  => $this->getOrder()->getShippingAddress()->getPostcode(),
					'weight'       => $params['weight'],
					'box'          => $params['box'],
					'xmlns' => 'http://www.canadapost.ca/ws/ship/rate'
				);

				$data = Mage::getModel('chcanpost2module/quote_param')->getParamsByQuote($this->getOrder()->getId(), $data, 'order');


				$rates = Mage::helper('chcanpost2module/rest_getRates')->getRates($data);

				$service_price = 0;

				if (!empty($rates)) {

					foreach ($rates as $rate) {

						if ($params['service_code'] == 'failure' || $rate['price'] < $service_price) {

							$service_price = $rate['price'];

							$params['service_code'] = $rate['code'];

						}

					}

				}

			}

			$service_info = Mage::helper('chcanpost2module/rest_service')->getInfo($params['service_code'], $this->getOrder()->getShippingAddress()->getCountry());

			if (!empty($service_info->options->option)) {

				$mandatory_options = array();

				$available_options = array();

				foreach ($service_info->options->option as $opt) {

					if (strtolower((string)$opt->mandatory) == 'true') {

						$mandatory_options[] = (string)$opt->{'option-code'};

					}

					$available_options[] = (string)$opt->{'option-code'};

				}

			}

			$quote_params = Mage::getModel('chcanpost2module/quote_param')->getParamsByQuote($this->getOrder()->getId(), array(), 'order');
			if(isset($quote_params['COD']) && $quote_params['COD'] == 0) {
				unset($quote_params['COD']);
			}

			if(isset($quote_params['cod']) && $quote_params['cod'] == 0) {
				unset($quote_params['cod']);
			}

			if (!empty($quote_params)) {

				$params['options'] = Mage::helper('chcanpost2module/option')->composeForOrder($quote_params, $this, $shipping_address, $mandatory_options, $available_options);

				$params['cp_office_id'] = $quote_params['office_id'];

			}

			$params['current_shipment_id'] = $magento_shipment_id;

			$response = Mage::helper('chcanpost2module/rest_shipment')->create($shipping_address, $this->getOrder(), $group_id, $params);

			$result = false;

			$this->xml = new SimpleXMLElement($response);

			if (!empty($this->xml->{'shipment-id'})
				//&& !empty($this->xml->{'tracking-pin'}) //hmmm - it seems this part is not always present
				&& !empty($this->xml->links)
			) {

				$this->saveShipmentInfo($manifest_id, $magento_shipment_id);

				$result = true;

			} else if (!empty($this->xml->message->description)) {

				Mage::helper('chcanpost2module')->log($this->xml->message->description);

				$this->error = $this->xml->message->description;

			}

		}

		return $result;

	}


	private function saveShipmentInfo($manifest_id, $magento_shipment_id)
	{

		$shipment_id = Mage::getModel('chcanpost2module/shipment')
			->setOrderId($this->getOrderId())
			->setShipmentId($this->xml->{'shipment-id'})
			->setStatus($this->xml->{'shipment-status'})
			->setTrackingPin($this->xml->{'tracking-pin'})
			->setManifestId($manifest_id)
			->setMagentoShipmentId($magento_shipment_id)
			->save()
			->getId();

		if (!empty($this->xml->links)) {

			foreach ($this->xml->links->link as $link) {

				Mage::getModel('chcanpost2module/link')
					->setCpShipmentId($shipment_id)
					->setUrl($link['href'])
					->setMediaType($link['media-type'])
					->setRel($link['rel'])
					->save();

			}

		}

		$track = Mage::getModel('sales/order_shipment_track')->addData(array(
			'carrier_code' => 'chcanpost2module',
			'title' => Mage::helper('chcanpost2module')->__('Shipment for order #').$this->getOrder()->getIncrementId(),
			'number' => $this->xml->{'tracking-pin'},
		));

		$this->addTrack($track);

	}


	/**
	 *
	 * @param Collinsharper_Canpost_Model_Shipment $cp_shipment
	 * @return type
	 */
	public function removeCpShipment($cp_shipment)
	{
		if ($cp_shipment->getId()) {

			$result = Mage::helper('chcanpost2module/rest_shipment')->void($cp_shipment->getId());

			$cp_shipment->delete();

			foreach ($this->getAllTracks() as $track) {

				$track->delete();

			}

		}

		return true;

	}


	public function getError()
	{

		return $this->error;

	}


	public function prepareGridCollection($manifest_id)
	{

		$collection = Mage::getModel('sales/order_shipment')->getCollection();

		$collection->getSelect()->columns(array('shipment_increment_id'=>'increment_id'));

		$collection->getSelect()->joinLeft(
			array('cs'=>Mage::getSingleton('core/resource')->getTableName('ch_canadapost_shipment')),
			'main_table.entity_id = cs.magento_shipment_id',
			array('manifest_id')
		);

		if (!empty($manifest_id)) { //view

			$manifest = Mage::getModel('chcanpost2module/manifest')->load($manifest_id);

			if ($manifest->getStatus() == 'pending') {

				$collection->addFieldToFilter('cs.manifest_id', array(array('eq' => $manifest_id), array('null'=>true)));

			} else {

				$collection->addFieldToFilter('cs.manifest_id', $manifest_id);

			}

			$shipment_collection  = Mage::getModel('chcanpost2module/shipment')->getCollection()->addFieldToFilter('manifest_id', $manifest_id);

			$shipment_collection->getSelect()->joinLeft(
				array('s' => Mage::getSingleton('core/resource')->getTableName('sales/shipment')),
				's.entity_id = main_table.magento_shipment_id',
				array('store_id')
			);


			if ($shipment_collection->count() > 0 && Mage::getStoreConfig('carriers/chcanpost2module/scope') == 0) {

				$collection->getSelect()->where('main_table.store_id='.$shipment_collection->getFirstItem()->getData('store_id'));

			}

		} else { //create

			$collection->addFieldToFilter('cs.manifest_id', array('null'=>true));

		}

		$collection->getSelect()->joinLeft(
			array('o'=>Mage::getSingleton('core/resource')->getTableName('sales_flat_order')),
			'main_table.order_id = o.entity_id',
			array(
				'order_increment_id' => 'o.increment_id',
				'order_created_at' => 'o.created_at',
				'ordered_by' => 'CONCAT(o.customer_firstname, \' \',o.customer_lastname)',
			)
		);

		$collection->getSelect()->distinct(true); //->group('cs.magento_shipment_id');

		$collection->addFieldToFilter('o.shipping_method', array('like' => 'chcanpost2module_%'));

		return $collection;

	}

}