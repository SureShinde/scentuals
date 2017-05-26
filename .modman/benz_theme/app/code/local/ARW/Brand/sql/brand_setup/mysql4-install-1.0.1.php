<?php
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE {$this->getTable('arw_brand')} (
	`brand_id` int(11) unsigned NOT NULL auto_increment,  
	 `name` varchar(255) NOT NULL default '',
	 `website` varchar(255) default '', 
	 `logo` varchar(255) default '',                                                                                                                        
	 `status` tinyint(11) default NULL,                            
	 `created_time` datetime default NULL,                         
	 `update_time` datetime default NULL,
	 PRIMARY KEY  (`brand_id`)                             
	) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;
CREATE TABLE {$this->getTable('arw_brand_store')} (                                
   `brand_id` int(11) unsigned NOT NULL,                      
   `store_id` smallint(5) unsigned NOT NULL,                          
   PRIMARY KEY  (`brand_id`,`store_id`),                      
   KEY `FK_BRAND_STORE_STORE` (`store_id`)                    
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Brand Stores';
 
 CREATE TABLE {$this->getTable('arw_brand_products')} (                                
	`brand_id` int(11) NOT NULL,                                 
	`product_id` smallint(5) unsigned NOT NULL,                                                      
	PRIMARY KEY  (`brand_id`,`product_id`),                      
	KEY `FK_BRAND_PRODUCTS` (`product_id`)                       
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Brand Products';

    ");
$installer->endSetup(); 