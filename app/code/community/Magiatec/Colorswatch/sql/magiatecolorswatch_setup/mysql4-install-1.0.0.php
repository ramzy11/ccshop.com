<?php
$installer = $this;
$installer->startSetup();

/**
 * Create table 'magiatec_colorswatch_product'
 */
if (!$this->tableExists($this->getTable('magiatecolorswatch/product'))) {

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('magiatecolorswatch/product')};

CREATE TABLE IF NOT EXISTS {$this->getTable('magiatecolorswatch/product')} (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Image ID',
  `product_super_attribute_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Product Super Attribute ID',
  `value_index` varchar(255) DEFAULT NULL COMMENT 'Value Index',
  `image` varchar(255) DEFAULT NULL COMMENT 'Image',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  PRIMARY KEY (`image_id`),
  KEY `IDX_MAGIATEC_COLORSWATCH_PRODUCT_PRODUCT_SUPER_ATTRIBUTE_ID` (`product_super_attribute_id`),
  KEY `IDX_MAGIATEC_COLORSWATCH_PRODUCT_STORE_ID` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Super Attribute Images Table';

ALTER TABLE `magiatec_colorswatch_product`
  ADD CONSTRAINT `FK_MAGIATEC_COLORSWATCH_PRODUCT_PRODUCT_SUPER_ATTRIBUTE_ID` FOREIGN KEY (`product_super_attribute_id`) REFERENCES {$installer->getTable('catalog/product_super_attribute_label')} (`product_super_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_MAGIATEC_COLORSWATCH_PRODUCT_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

}
/**
 * Create table 'magiatec_colorswatch_attribute'
 */
if (!$this->tableExists($this->getTable('magiatecolorswatch/attribute'))) {

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('magiatecolorswatch/attribute')};

CREATE TABLE IF NOT EXISTS {$this->getTable('magiatecolorswatch/attribute')} (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Image ID',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Attribute Id',
  `option_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Option Id',
  `image` varchar(255) DEFAULT NULL COMMENT 'Image',
  PRIMARY KEY (`image_id`),
  KEY `IDX_MAGIATEC_COLORSWATCH_ATTRIBUTE_ATTRIBUTE_ID` (`attribute_id`),
  KEY `IDX_MAGIATEC_COLORSWATCH_ATTRIBUTE_OPTION_ID` (`option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Attribute Option Images Table';

ALTER TABLE `magiatec_colorswatch_attribute`
  ADD CONSTRAINT `FK_MAGIATEC_COLORSWATCH_ATTR_ATTR_ID_EAV_ATTR_ATTR_ID` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav/attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_MAGIATEC_COLORSWATCH_ATTR_OPT_ID_EAV_ATTR_OPT_OPT_ID` FOREIGN KEY (`option_id`) REFERENCES {$installer->getTable('eav/attribute_option')} (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE;

");

}

$installer->endSetup();
