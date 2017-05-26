<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('news')};
CREATE TABLE {$this->getTable('news')} (
  `news_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `texto` text NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `datefrom` date default NULL,
  `dateto` date default NULL,
  `status` tinyint(1) NOT NULL default '0',
  `amigable` varchar(255) NOT NULL default '',
  PRIMARY KEY USING BTREE (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
