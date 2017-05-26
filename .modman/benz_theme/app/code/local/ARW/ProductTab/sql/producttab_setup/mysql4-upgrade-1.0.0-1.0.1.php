<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE `{$this->getTable('arw_producttab_tab')}` 
	ADD COLUMN `arw_identifier` varchar(255) default NULL  AFTER `arw_name`;
");
$installer->endSetup(); 
