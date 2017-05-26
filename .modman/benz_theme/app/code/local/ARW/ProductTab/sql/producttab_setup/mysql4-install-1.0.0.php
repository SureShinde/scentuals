<?php
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('producttab/tab')} (
		`arw_tab_id` int(11) unsigned NOT NULL auto_increment,  
		`arw_name` varchar(255) NOT NULL default '',                                                                                                                     
		`arw_status` tinyint(11) default NULL,
		`arw_use_default` TINYINT(5)  NOT NULL default '0',
		`arw_enable_scroll`  TINYINT(5)  default '1',
		`arw_auto_play`  TINYINT(5)  default '1',
		`arw_animation_loop`  TINYINT(5)  default '1',
		`arw_enable_navigation`  TINYINT(5)  default '0',
		`arw_margin`  INT(5) default '30',
		`arw_enable_dots`  TINYINT(5) default '0',
		`arw_speed`  INT(5) default '2000',
		`arw_lazy_loading`  TINYINT(5)  default '1',
		`arw_responsive`  VARCHAR(50) default '1170:4,970:3,750:2',
		`arw_limit`  TINYINT(5) default '15',
		`arw_row`  TINYINT(5) default '2',
		`arw_column`  TINYINT(5) default '3',		
		PRIMARY KEY  (`arw_tab_id`)                             
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'All Tabs';

CREATE TABLE {$this->getTable('arw_producttab_store')} (
   `arw_tab_id` int(11) unsigned NOT NULL,                      
   `store_id` smallint(5) unsigned NOT NULL,   
	PRIMARY KEY  (`arw_tab_id`,`store_id`), 
	CONSTRAINT `FK_CATEGORYSLIDER_STORE` FOREIGN KEY (`arw_tab_id`) REFERENCES `{$this->getTable('arw_producttab_tab')}` (`arw_tab_id`) ON DELETE CASCADE ON UPDATE CASCADE	    
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tab Stores';
 
CREATE TABLE IF NOT EXISTS  {$this->getTable('arw_producttab_product')} (
	`id` int(11) unsigned NOT NULL auto_increment,  
	`arw_tab_id` int(11) NOT NULL,                                 
	`product_type` smallint(5) unsigned NOT NULL,
	`product_sort_type` smallint(5) unsigned,   
	`current_category_type` smallint(5) unsigned,
	`product_data` TEXT ,
	PRIMARY KEY  (`id`,`arw_tab_id`),    
	CONSTRAINT `FK_PRODUCTTAB_ITEM` FOREIGN KEY (`arw_tab_id`) REFERENCES `{$this->getTable('arw_producttab_tab')}` (`arw_tab_id`) ON DELETE CASCADE ON UPDATE CASCADE	
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Products';
    ");
$installer->endSetup(); 