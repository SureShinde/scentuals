<?php

/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Adminhtml_System_Config_Source_Yesno extends Mage_Core_Model_Config_Data
{

    public function save()
    {
        
        $result = true;
        
        if (!empty($_POST['groups']['chcanpost2module']['fields'])) {
            
            $cp_config = $_POST['groups']['chcanpost2module']['fields'];
            
            if ($cp_config['active']['value'] == 1) {
                    
                $result = false;
                
                $api_customer_number = Mage::getStoreConfig('carriers/chcanpost2module/api_customer_number');
                
                $api_url = Mage::getStoreConfig('carriers/chcanpost2module/api_url');
                
                $api_login = Mage::getStoreConfig('carriers/chcanpost2module/api_login');
                
                $api_password = Mage::getStoreConfig('carriers/chcanpost2module/api_password');
                
                if (!empty($api_customer_number) 
                    && (
                        (
                        !empty($api_url)
                        && !empty($api_login)
                        && !empty($api_password)          
                        ) ||
                        (
                        !empty($cp_config['api_test_url']['value'])
                        && !empty($cp_config['api_test_login']['value'])
                        && !empty($cp_config['api_test_password']['value'])          
                        )
                    )
                   ) {

                    $result = true;

                }
                
            }
            
        }
        
        if (!$result) {
            
            Mage::throwException(Mage::helper('chcanpost2module')->__('Module can not be enabled without proper Canadapost credentials. Please, signup to Canadapost first'));
            
        }

        return parent::save(); 
        
    }

}
