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

$installer->addAttribute('catalog_category', 'arwmenu_category_icon', array(
    'group'				=> 'Main Menu',
    'label'				=> 'Category Menu Icon',
    'note'				=> "Show Icon Category",
    'type'				=> 'varchar',
    'input'				=> 'select',
    'source'			=> 'megamenu/system_config_source_category_attribute_source_categoryicon',
    'visible'			=> true,
    'required'			=> false,
    'backend'			=> '',
    'frontend'			=> '',
    'searchable'		=> false,
    'filterable'		=> false,
    'comparable'		=> false,
    'user_defined'		=> true,
    'visible_on_front'	=> true,
    'wysiwyg_enabled'	=> false,
    'is_html_allowed_on_front'	=> false,
    'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));


$installer->endSetup();