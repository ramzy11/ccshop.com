<?php
class Magiatec_Colorswatch_Model_Observer
{
    public function saveProductSwatches(Varien_Event_Observer $observer)
    {
        $_product = $observer->getEvent()->getObject();

        $toDelete = Mage::app()->getRequest()->getPost('magiatecolorswatch_delete');
        if (!empty($toDelete)) {
            foreach ($toDelete as $product_super_attribute_id => $values) {
                foreach ($values as $value_index => $item) {
                    $item = Mage::getModel('magiatecolorswatch/product')->load($value_index, 'value_index');
                    if ($item->getId()) {
                        @unlink(Mage::helper('magiatecolorswatch')->getSwatchesFile($item->getImage()));
                        $item->delete();
                    }
                }
            }
        }

        if (isset($_FILES['magiatecolorswatch'])) {
            $files = $_FILES['magiatecolorswatch'];

            $allowedExtensions = Mage::helper('magiatecolorswatch')->getAllowedExtensions();
            $destinationFolder = Mage::helper('magiatecolorswatch')->getSwatchesFile();
            $swatch = Mage::getModel('magiatecolorswatch/product');

            foreach ($files['name'] as $product_super_attribute_id => $items) {
                foreach ($items as $value_index => $item) {
                    if ($files['error'][$product_super_attribute_id][$value_index] == UPLOAD_ERR_OK) {
                        try {
                            $tmp_name = $files['tmp_name'][$product_super_attribute_id][$value_index];
                            $uploader = new Varien_File_Uploader(array(
                                'name' => $item,
                                'tmp_name' => $tmp_name,
                            ));
                            $uploader->setAllowedExtensions($allowedExtensions);
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(false);

                            $result = $uploader->save($destinationFolder);
                            if ($result) {
                                $swatch->addImage(array(
                                    'product_super_attribute_id' => $product_super_attribute_id,
                                    'value_index' => $value_index,
                                    'image' => $uploader->getUploadedFileName()
                                ));
                            }
                        } catch (Exception $e) {
                            Mage::getSingleton('core/session')->addError($e->getMessage());
                        }
                    }
                }
            }
            $swatch->saveImages();
        }

        // sort image
       $toSort = Mage::app()->getRequest()->getPost('magiatecolorswatch_sort');
       if (!empty($toSort)) {
            $writeAdapter =  Mage::getSingleton('core/resource')->getConnection('core_write');
            foreach ($toSort as $product_super_attribute_id => $values) {
                foreach ($values as $value_index => $item) {
                   $sql = 'UPDATE '.$writeAdapter->getTableName('magiatec_colorswatch_product').' SET `sort`='.intval($item)." where `product_super_attribute_id`='".$product_super_attribute_id."' AND `value_index`='".$value_index."'";
                    $writeAdapter->query($sql);
                }
            }
       }

        return $this;
    }

    public function saveAttributeSwatches(Varien_Event_Observer $observer)
    {
        $_attribute = $observer->getEvent()->getAttribute();

        $toDelete = Mage::app()->getRequest()
                ->getPost('attribute_magiatecolorswatch_delete');

        if (!empty($toDelete)) {
            foreach ($toDelete as $optionId => $value) {
                $item = Mage::getModel('magiatecolorswatch/attribute')
                        ->load($optionId, 'option_id');
                if ($item->getId()) {
                    @unlink(Mage::helper('magiatecolorswatch')
                            ->getSwatchesFile($item->getImage()));
                    $item->delete();
                }
            }
        }

        if (isset($_FILES['attribute_magiatecolorswatch'])) {
            $files = $_FILES['attribute_magiatecolorswatch'];

            $allowedExtensions = Mage::helper('magiatecolorswatch')->getAllowedExtensions();
            $destinationFolder = Mage::helper('magiatecolorswatch')->getSwatchesFile();
            $swatch = Mage::getModel('magiatecolorswatch/attribute');

            foreach ($files['name'] as $id => $item) {
                if ($files['error'][$id] == UPLOAD_ERR_OK) {
                    try {
                        $tmp_name = $files['tmp_name'][$id];
                        $uploader = new Varien_File_Uploader(array(
                            'name' => $item,
                            'tmp_name' => $tmp_name,
                        ));
                        $uploader->setAllowedExtensions($allowedExtensions);
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);

                        $result = $uploader->save($destinationFolder);
                        if ($result) {
                            $swatch->addImage(array(
                                'attribute_id' => $_attribute->getId(),
                                'option_id' => $id,
                                'image' => $uploader->getUploadedFileName()
                            ));
                        }
                    } catch (Exception $e) {
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                    }
                }
            }
            $swatch->saveImages();
        }
        return $this;
    }

    public function loadProductSwatchesToCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($collection instanceof Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection AND $collection->count()) {

            $swatchesAttribute = Mage::getModel('magiatecolorswatch/attribute')
                ->getResourceCollection()
                ->addFieldToFilter('attribute_id', array(
                    'in' => $collection->getColumnValues('attribute_id')
                ));

            $swatchesProduct = Mage::getModel('magiatecolorswatch/product')
                ->getResourceCollection()
                ->addFieldToFilter('product_super_attribute_id', array(
                    'in' => $collection->getAllIds()
                ));

            foreach($collection as $item) {
                foreach ($swatchesAttribute as $swatch) {
                    $images = (array) $item->getImages();
                    $images[$swatch->getData('option_id')] = $swatch;
                    $item->setImages($images);
                }
                $superAttrId = $item->getData('product_super_attribute_id');
                $_productSwatches = $swatchesProduct->getItemsByColumnValue('product_super_attribute_id', $superAttrId);
                foreach ($_productSwatches as $swatch) {
                    $images = (array) $item->getImages();
                    $images[$swatch->getData('value_index')] = $swatch;
                    $item->setImages($images);
                }
            }
        }
        return $this;
    }

    public function loadAttributeSwatchesToCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($collection instanceof Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
                AND $collection->count()) {
            $firstItem = $collection->getFirstItem();

            $swatchesCollection = Mage::getModel('magiatecolorswatch/attribute')
                ->getResourceCollection()
                ->addFieldToFilter('attribute_id', array(
                    'in' => $firstItem->getAttributeId()
                ));

            foreach ($swatchesCollection as $swatch) {
                $item = $collection->getItemById($swatch->getOptionId());
                is_object($item) and $item->setImage($swatch->getImage());
            }
        }
    }

    public function addSwatchesHtml(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (Mage::getStoreConfig('magiatecolorswatch/settings/enabled')
                AND $block instanceof Mage_Catalog_Block_Product_View_Type_Configurable
                AND $block->getBlockAlias() == 'options_configurable') {
            $transport = $observer->getEvent()->getTransport();

            if ( preg_match_all('/<select name="super_attribute\[(\d+)\]" [^>]*>/i',
                    $transport->getHtml(), $matches, PREG_SET_ORDER) ) {

                $attributeIds = explode(',', Mage::getStoreConfig('magiatecolorswatch/settings/attributes'));

                $html = $transport->getHtml();
                foreach ($matches as $match) {
//                    $style = in_array($match[1], $attributeIds) ? 'display:none;' : '';
                    $additionalCssClass = in_array($match[1], $attributeIds) ? '' : 'no-swatch';
                    $html = preg_replace('/(<select name="super_attribute\['.$match[1].'\]" [^>]*>)/i', '<div class="magiatec-swatches ' .
                        $additionalCssClass . '"><div style="clear:both;"></div></div><div class="magiatec-options-wrapper" style="display:none">$1', $html);
                }

                $html = preg_replace('/(<\/select>)/i', '$1</div>', $html);

                $transport->setHtml($html);
            }
        }
    }
}
