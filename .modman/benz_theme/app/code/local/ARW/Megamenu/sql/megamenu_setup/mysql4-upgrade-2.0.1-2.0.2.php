<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     ARW
 * @package      ARW_arexworks
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 arexworks.com. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       arexworks.com
 * @email        support@arexworks.com
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
$installer = $this;
$installer->startSetup();

$attributes = array(
    'thumbnail' => array(
        'type'       => 'varchar',
        'label'      => 'Thumbnail Image',
        'input'      => 'image',
        'backend'    => 'catalog/category_attribute_backend_image',
        'required'   => false,
        'sort_order' => 5,
        'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'group'      => 'General Information',
    ),
    'custom_classes' => array(
        'type'          =>  'text',
        'label'         =>  'Custom Classes',
        'input'         =>  'text',
        'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'       =>  true,
        'required'      =>  false,
        'user_defined'  =>  true,
        'default'       =>  "",
        'group'         =>  "Main Menu"
    ),

);

foreach ($attributes as $code => $data) {
    $this->addAttribute(Mage_Catalog_Model_Category::ENTITY, $code, $data);
}


$installer->endSetup();