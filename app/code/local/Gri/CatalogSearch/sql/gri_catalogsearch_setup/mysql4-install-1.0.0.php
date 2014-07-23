<?php
$installer = $this;
$installer->getConnection()->addColumn($installer->getTable('catalogsearch/search_query'), 'promoted_terms', 'varchar(128) NULL');
?>