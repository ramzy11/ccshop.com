<?php

/**
 * @method string getImage() Get Image Filename
 * @method integer getIsActive() Get Active Status
 * @method Gri_FlashSale_Model_Resource_FlashSale getResource() Get Resource Model
 * @method string getSmallImage() Get Small Image Filename
 * @method Gri_FlashSale_Model_FlashSale setImage() Set Image Filename
 * @method Gri_FlashSale_Model_FlashSale setIsActive() Set Active Status
 * @method Gri_FlashSale_Model_FlashSale setSmallImage() Set Small Image Filename
 */
class Gri_FlashSale_Model_FlashSale extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'flash_sale';
    protected $_protectedFields = array(
        'flash_sale_id',
        'created_at',
        'updated_at',
    );
    protected $_parentProducts = array();
    protected $_products = array();

    protected function _construct()
    {
        $this->_init('gri_flashsale/flashSale');
    }

    /**
     * @return Gri_FlashSale_Model_Resource_Product_Collection
     */
    public function getAssociatedProducts()
    {
       // if ($this->getData('flash_sale_products') === NULL) {
            /* @var $products Gri_FlashSale_Model_Resource_Product_Collection */
            $products = Mage::getResourceModel('gri_flashsale/product_collection')
                ->setStoreId(Mage::app()->getStore()->getId());
            $products->joinTable(array('fp' => 'gri_flashsale/flashsale_product'), 'parent_id=entity_id', array(
                'flash_sale_id' => 'flash_sale_id',
                'parent_id' => 'parent_id',
                'final_price' => 'MAX(fp.price)',
                'minimal_price' => 'minimal_price',
                'flash_sale_price_calculated' => 'flash_sale_id',
                'product_url' => 'CONCAT("flashsale/product/view/item/", e.sku)',
                'flash_sale_parent_qty' => 'parent_qty',
            ), array('flash_sale_id' => $this->getId()), 'inner')
                ->joinTable(array('fpo' => 'gri_flashsale/flashsale_product_ordered'), 'parent_id=entity_id', array(
                    'flash_sale_parent_qty_ordered' => 'parent_qty_ordered',
                ), array('flash_sale_id' => $this->getId()), 'left');

            if( $attributeSetId = intval(Mage::app()->getRequest()->getParam('attribute_set_id')) ) {
                $products->addFieldToFilter('attribute_set_id', $attributeSetId);
            }
            $products->getSelect()->group('fp.parent_id');

            return $products;
         //   $this->setData('flash_sale_products', $products);
       // }

       // return $this->getData('flash_sale_products');
    }

    /**
     *  @return Gri_FlashSale_Model_Resource_Product_Collection
     */
    public function getAssociatedProductsWithoutAttributeSetId()
    {
        /* @var $products Gri_FlashSale_Model_Resource_Product_Collection */
        $products = Mage::getResourceModel('gri_flashsale/product_collection')
                ->setStoreId(Mage::app()->getStore()->getId());
        $products->joinTable(array('fp' => 'gri_flashsale/flashsale_product'), 'parent_id=entity_id', array(
                'flash_sale_id' => 'flash_sale_id',
                'parent_id' => 'parent_id',
                'final_price' => 'MAX(fp.price)',
                'minimal_price' => 'minimal_price',
                'flash_sale_price_calculated' => 'flash_sale_id',
                'product_url' => 'CONCAT("flashsale/product/view/item/", e.sku)',
                'flash_sale_parent_qty' => 'parent_qty',
        ), array('flash_sale_id' => $this->getId()), 'inner')
                ->joinTable(array('fpo' => 'gri_flashsale/flashsale_product_ordered'), 'parent_id=entity_id', array(
                    'flash_sale_parent_qty_ordered' => 'parent_qty_ordered',
             ), array('flash_sale_id' => $this->getId()), 'left');

        $products->getSelect()->group('fp.parent_id');

        return $products;
    }



    protected function _afterDelete()
    {
        is_file($filename = $this->getHelper()->getImagePath($this->getImage())) and unlink($filename);
        is_file($filename = $this->getHelper()->getImagePath($this->getSmallImage())) and unlink($filename);
        return parent::_afterDelete();
    }

    protected function _afterSave()
    {
        if ($this->getIsActive()) {
            $this->getResource()->activate($this);
            $this->updateFlashSaleProducts();
        }
        return parent::_afterSave();
    }

    protected function _beforeSave()
    {
        $now = Varien_Date::now();
        $this->getCreatedAt() or $this->setCreatedAt($now);
        $this->setUpdatedAt($now);

        if (is_array($image = $this->getImage())) {
            isset($image['value']) and $this->setImage($image['value']);
            if (isset($image['delete']) && $image['delete']) {
                $this->setImage('');
                is_file($filename = $this->getHelper()->getImagePath($this->getOrigData('image'))) and unlink($filename);
            }
        }
        if (is_array($image = $this->getSmallImage())) {
            isset($image['value']) and $this->setSmallImage($image['value']);
            if (isset($image['delete']) && $image['delete']) {
                $this->setSmallImage('');
                is_file($filename = $this->getHelper()->getImagePath($this->getOrigData('small_image'))) and unlink($filename);
            }
        }
        if (is_array($image = $this->getMobileImage())) {
            isset($image['value']) and $this->setMobileImage($image['value']);
            if (isset($image['delete']) && $image['delete']) {
                $this->setMobileImage('');
                is_file($filename = $this->getHelper()->getImagePath($this->getOrigData('mobile_image'))) and unlink($filename);
            }
        }
        if (is_array($image = $this->getMobileSmallImage())) {
            isset($image['value']) and $this->setMobileSmallImage($image['value']);
            if (isset($image['delete']) && $image['delete']) {
                $this->setMobileSmallImage('');
                is_file($filename = $this->getHelper()->getImagePath($this->getOrigData('mobile_small_image'))) and unlink($filename);
            }
        }

        // cht
        if (is_array($image = $this->getImageCht())) {
            isset($image['value']) and $this->setImageCht($image['value']);
            if (isset($image['delete']) && $image['delete']) {
                $this->setImageCht('');
                is_file($filename = $this->getHelper()->getImagePath($this->getOrigData('image_cht'))) and unlink($filename);
            }
        }
        if (is_array($image = $this->getSmallImageCht())) {
            isset($image['value']) and $this->setSmallImageCht($image['value']);
            if (isset($image['delete']) && $image['delete']) {
                $this->setSmallImageCht('');
                is_file($filename = $this->getHelper()->getImagePath($this->getOrigData('small_image'))) and unlink($filename);
            }
        }
        if (is_array($image = $this->getMobileImageCht())) {
            isset($image['value']) and $this->setMobileImageCht($image['value']);
            if (isset($image['delete']) && $image['delete']) {
                $this->setMobileImageCht('');
                is_file($filename = $this->getHelper()->getImagePath($this->getOrigData('mobile_image_cht'))) and unlink($filename);
            }
        }
        if (is_array($image = $this->getMobileSmallImageCht())) {
            isset($image['value']) and $this->setMobileSmallImageCht($image['value']);
            if (isset($image['delete']) && $image['delete']) {
                $this->setMobileSmallImageCht('');
                is_file($filename = $this->getHelper()->getImagePath($this->getOrigData('mobile_small_image_cht'))) and unlink($filename);
            }
        }

        return parent::_beforeSave();
    }

    /**
     * @return Gri_FlashSale_Model_FlashSale_Product
     */
    public function getFlashSaleProductModel()
    {
        return Mage::getModel('gri_flashsale/flashSale_product')->setFlashSale($this);
    }

    /**
     * @return Gri_FlashSale_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('gri_flashsale');
    }

    /**
     * @param integer $parentId
     * @return Gri_FlashSale_Model_FlashSale_Product
     */
    public function getParentProductById($parentId)
    {
        if (!isset($this->_parentProducts[$parentId])) {
            $product = $this->getFlashSaleProductModel()->load($parentId, 'parent_id');
            !$product->getId() || !$product->getIsActive() and $product = FALSE;
            $this->_parentProducts[$parentId] = $product;
        }
        return $this->_parentProducts[$parentId];
    }

    public function getParsedDefinition()
    {
        if ($this->getData('parsed_definition') === NULL) {
            $result = array();
            $definition = explode("\n", str_replace("\n\n", "\n", str_replace("\r", "\n", $this->getDefinition())));
            $statements = $this->getParsingStatements();

            $qtys = array();
            $parentPrices = array();
            $minimalPrices = array();
            foreach ($definition as $row) {
                $row = array_map('trim', explode(',', $row));
                // Reset sku, color, size, quantity and price
                $sku = array_shift($row);
                $color = $size = $qty = $price = $hasParentPrice = FALSE;
                // Reset database query statement
                /* @var $stmt Varien_Db_Statement_Pdo_Mysql */
                $stmt = FALSE;
                $params = NULL;
                switch ($cols = count($row)) {
                    case 2:
                        list($qty, $price) = $row;
                        $stmt = $statements['all'];
                        $params = array(':parent_sku' => $sku);
                        $hasParentPrice = TRUE;
                        break;
                    case 3:
                        list($color, $qty, $price) = $row;
                        $stmt = $statements['color'];
                        $params = array(':parent_sku' => $sku, ':color' => $color);
                        break;
                    case 4:
                        list($color, $size, $qty, $price) = $row;
                        $stmt = $statements['color_size'];
                        $params = array(':parent_sku' => $sku, ':color' => $color, ':size' => $size);
                        break;
                }
                // Skip bad definition format and bad price
                if (!$stmt || !$price = (int)$price) continue;
                // Skip bad quantity
                if ($qty !== '' && ($qty = $qty * 1) < 0) continue;

                $stmt->execute($params);
                $products = $stmt->fetchAll();
                foreach ($products as $product) {
                    // Assign parent qty if specified
                    if (!$parentId = $product['parent_id']) {
                        $qtys['parent'][$parentId = $product['parent_id'] = $product['entity_id']] = array('qty' => $qty, 'assigned' => TRUE);
                    }
                    $colorCode = $product['color_code'];
                    // Make sure parent qty and color qty are ready
                    isset($qtys['parent'][$parentId]) or
                        $qtys['parent'][$parentId] = array('qty' => 0, 'assigned' => FALSE);
                    isset($qtys['color'][$parentId][$colorCode]) or
                        $qtys['color'][$parentId][$colorCode] = array('qty' => 0, 'assigned' => FALSE);

                    // Assign color qty if specified
                    if ($cols < 4) {
                        $qtys['color'][$parentId][$colorCode] = array('qty' => $qty, 'assigned' => TRUE);
                    }
                    // Calculate color qty if not specified
                    !$qtys['color'][$parentId][$colorCode]['assigned'] and $qtys['color'][$parentId][$colorCode]['qty'] += $qty;

                    $product['price'] = $price;
                    $product['qty'] = $qty;
                    if ($hasParentPrice) {
                        $parentPrices[$parentId] = isset($parentPrices[$parentId]) ?
                            max($parentPrices[$parentId], $price) : $price;
                    }
                    $minimalPrices[$parentId] = isset($minimalPrices[$parentId]) ?
                        min($minimalPrices[$parentId], $price) : $price;
                    $product['product_id'] = $product['entity_id'];
                    $product['flash_sale_id'] = $this->getId();
                    $result[$product['entity_id']] = $product;
                }
            }

            // Calculate parent qty if not specified
            foreach ($qtys['parent'] as $parentId => &$parentQty) {
                if (!$parentQty['assigned']) foreach ($qtys['color'][$parentId] as $colorQty) {
                    $parentQty['qty'] += $colorQty['qty'];
                }
            }

            // Apply parent quantity, parent price and minimal price
            foreach ($result as $k => $product) {
                $result[$k]['parent_qty'] = $qtys['parent'][$parentId = $product['parent_id']]['qty'];
                $result[$k]['color_qty'] = $qtys['color'][$parentId][$product['color_code']]['qty'];
                $result[$k]['parent_price'] = isset($parentPrices[$parentId]) ?
                    $parentPrices[$parentId] : NULL;
                $result[$k]['minimal_price'] = $minimalPrices[$parentId];
            }
            $this->setData('parsed_definition', $result);
        }
        return $this->getData('parsed_definition');
    }

    public function getParsingStatements()
    {
        /* @var $productModel Gri_CatalogCustom_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        // Prepare database query statement
        $select = $productModel->getCollection()
            ->joinAttribute('color_code', 'catalog_product/color_code', 'entity_id', NULL, 'left', 0)
            ->joinAttribute('size_clothing', 'catalog_product/size_clothing', 'entity_id', NULL, 'left', 0)
            ->joinAttribute('size_shoes', 'catalog_product/size_shoes', 'entity_id', NULL, 'left', 0)
            ->joinAttribute('color_filter_1', 'catalog_product/color_filter_1', 'entity_id', NULL, 'left', 0)
            ->joinAttribute('color_filter_2', 'catalog_product/color_filter_2', 'entity_id', NULL, 'left', 0)
            ->joinTable(array('cl' => 'eav/attribute_option_value'), 'option_id=color_code', array('color_label' => 'value'), array('store_id' => 0), 'left')
            ->joinTable(array('scl' => 'eav/attribute_option_value'), 'option_id=size_clothing', array('size_clothing_label' => 'value'), array('store_id' => 0), 'left')
            ->joinTable(array('ssl' => 'eav/attribute_option_value'), 'option_id=size_shoes', array('size_shoes_label' => 'value'), array('store_id' => 0), 'left')
            ->joinTable(array('l' => 'catalog/product_super_link'), 'product_id=entity_id', array('parent_id' => 'parent_id'), NULL, 'left')
            ->joinTable(array('le' => 'catalog/product'), 'entity_id=parent_id', array('parent_sku' => 'sku'), NULL, 'left')
            ->addFieldToFilter('type_id', 'simple')
            ->getSelectSql(TRUE);

        $where = array(
            'all' => array('(`le`.`sku` = :parent_sku OR `e`.`sku` = :parent_sku)'),
            'color' => array('`le`.`sku` = :parent_sku', '`cl`.`value` = :color'),
            'color_size' => array('`le`.`sku` = :parent_sku', '`cl`.`value` = :color', '(`scl`.`value` = :size OR `ssl`.`value` = :size)'),
        );
        $statements = array();
        foreach ($where as $type => $condition) {
            $statements[$type] = $productModel->getResource()->getReadConnection()
                ->prepare($select . ' AND ' . implode(' AND ', $condition) . ' GROUP BY e.entity_id');
        }
        return $statements;
    }

    /**
     * @param integer $productId
     * @return Gri_FlashSale_Model_FlashSale_Product
     */
    public function getProductById($productId)
    {
        if (!isset($this->_products[$productId])) {
            $product = $this->getFlashSaleProductModel()->load($productId, 'product_id');
            !$product->getId() || !$product->getIsActive() and $product = FALSE;
            $this->_products[$productId] = $product;
        }
        return $this->_products[$productId];
    }


    /**
     * @param integer $productId
     * @return Gri_FlashSale_Model_FlashSale_Product
     */
    public function getIsFlashSaleProductByParentId($parentProductId)
    {
        if (!isset($this->_products[$parentProductId])) {
            /* @var $activeFlashSale Gri_FlashSale_Model_FlashSale */
            $activeFlashSale = Mage::helper('gri_flashsale')->getActiveFlashSale();

            $products = $activeFlashSale->getAssociatedProducts();

            if( $items = $products->getItemsByColumnValue('parent_id', $parentProductId)){
                $this->_products[$parentProductId] = TRUE;
            }
        }
        return isset($this->_products[$parentProductId]) ? $this->_products[$parentProductId] : FALSE;
    }

    public function isProtectedField($field)
    {
        return in_array($field, $this->_protectedFields);
    }

    public function loadActiveFlashSale()
    {
        $this->load($this->getResource()->getActiveFlashSaleId());
        return $this;
    }

    public function updateFlashSaleProducts()
    {
        if ($definition = $this->getParsedDefinition()) {
            $flashSaleProductModel = $this->getFlashSaleProductModel();
            $flashSaleProductModel->getResource()->removeAll()->insertMultiple($definition);
        }
    }
}
