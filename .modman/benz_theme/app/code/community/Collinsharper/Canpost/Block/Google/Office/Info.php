<?php

/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Block_Google_Office_Info extends Mage_Core_Block_Template
{
    
    public function getList()
    {
        
        $offices = array(
            array(
                'address' => '700 Park Crescent, New Westminster, BC, Canada',
                'name' => 'CP office 1'
            ),
            array(
                'address' => '500 Beatty St, Vancouver, BC, Canada',
                'name' => 'CP office 2'
            ),
        );
        
    }
    
}
