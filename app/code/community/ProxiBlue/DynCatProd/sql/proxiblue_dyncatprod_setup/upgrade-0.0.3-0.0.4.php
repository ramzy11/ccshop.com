<?php
$installer = $this;
$installer->startSetup();

$installer->installEntities();

$installer->run("

DROP TABLE IF EXISTS {$installer->getTable('catalog_category_dynamic_product_index')};
CREATE TABLE {$installer->getTable('catalog_category_dynamic_product_index')} (
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Category ID',
  `product_ids` text NOT NULL DEFAULT '' COMMENT 'Product IDs',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  PRIMARY KEY (`category_id`,`store_id`),
  CONSTRAINT `FK_CAT_CAT_DYN_PRODUCT_INDEX_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CAT_CTGR_DYN_PRD_IDX_CTGR_ID_CAT_CTGR_ENTT_ENTT_ID` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Catalog Category Dynamic Product Index';

DROP TABLE IF EXISTS {$installer->getTable('catalog_category_dynamic_product_indexer_idx')};
CREATE TABLE {$installer->getTable('catalog_category_dynamic_product_indexer_idx')} (
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Category ID',
  `product_ids` text NOT NULL DEFAULT '' COMMENT 'Product IDs',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  PRIMARY KEY (`category_id`,`store_id`),
  CONSTRAINT `FK_CAT_CAT_DYN_PRODUCT_INDEX_STORE_ID_CORE_STORE_STORE_ID_IDX` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CAT_CTGR_DYN_PRD_IDX_CTGR_ID_CAT_CTGR_ENTT_ENTT_ID_IDX` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Catalog Category Dynamic Product Index Idx';

");


$installer->endSetup();
