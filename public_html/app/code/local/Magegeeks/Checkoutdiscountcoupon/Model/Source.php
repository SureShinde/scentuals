<?php
class Magegeeks_Checkoutdiscountcoupon_Model_Source
{
  public function toOptionArray()
  {
    return array(
      
      array('value' => '3', 'label' => 'Add after Billing/shipping Address'),
      array('value' => '4', 'label' =>'Add after Shipping Methods'),
	array('value' => '5', 'label' =>'Add after Payment Methods'),
     );
  }
}

