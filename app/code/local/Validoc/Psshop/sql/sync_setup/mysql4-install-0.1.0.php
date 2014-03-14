<?php
/**
 * Created by PhpStorm.
 * User: TOSHIBA
 * Date: 2/27/14
 * Time: 4:42 PM
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('psshop/synchronizaction_history')}`;
CREATE TABLE `{$this->getTable('psshop/synchronizaction_history')}` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'History Id',
  `profile_id` int(10) unsigned NOT NULL COMMENT 'Profile Id',
  `action_code` varchar(64) DEFAULT NULL COMMENT 'Action Code',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'User Id',
  `performed_at` timestamp NULL DEFAULT NULL COMMENT 'Performed At',
  PRIMARY KEY (`history_id`),
  CONSTRAINT `FK_SYNCHRONIZATION_WSPRICINGFEED_HISTORY` FOREIGN KEY (`profile_id`) REFERENCES `{$this->getTable('dataflow/profile')}` (`profile_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();