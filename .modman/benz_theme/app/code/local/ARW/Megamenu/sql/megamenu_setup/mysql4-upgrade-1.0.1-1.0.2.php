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

$installer->addAttribute('catalog_category', 'arwmenu_cat_label_text_color', array(
    'group'				=> 'Main Menu',
    'label'				=> 'Category Label Text Color',
    'note'				=> "Category Label Text Color",
    'type'				=> 'text',
	'input_renderer'    => 'megamenu/adminhtml_renderer_catlbcolor',
    'visible'			=> true,
    'required'			=> false,
    'backend'			=> '',
    'frontend'			=> '',
    'searchable'		=> false,
    'filterable'		=> false,
    'comparable'		=> false,
    'user_defined'		=> true,
    'visible_on_front'	=> true,
    'wysiwyg_enabled'	=> true,
    'is_html_allowed_on_front'	=> true,
    'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));


$installer->endSetup();