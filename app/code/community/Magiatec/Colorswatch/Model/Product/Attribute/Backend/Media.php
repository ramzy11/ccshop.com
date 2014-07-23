<?php

class Magiatec_Colorswatch_Model_Product_Attribute_Backend_Media extends Mage_Catalog_Model_Product_Attribute_Backend_Media
{

    protected function _getNotDuplicatedFilename($fileName, $dispretionPath)
    {
        if (Mage::app()->getRequest()->getRouteName() == 'adminhtml' &&
            Mage::app()->getRequest()->getControllerName() == 'system_convert_profile') return $fileName;
        return parent::_getNotDuplicatedFilename($fileName, $dispretionPath);
    }

    /**
     * Load attribute data after product loaded
     *
     * @param Mage_Catalog_Model_Product $object
     */
    public function afterLoad($object)
    {
        parent::afterLoad($object);
        $attrCode = $this->getAttribute()->getAttributeCode();
        /* @var $swatchHelper Magiatec_Colorswatch_Helper_Swatch */
        $swatchHelper = Mage::helper('magiatecolorswatch/swatch');
        $swatchHelper->setCurrentProduct($object);
        $swatchHelper->associateSwatchPictures($object, $attrCode);
    }

    /**
     * @param Gri_CatalogCustom_Model_Product $object
     */
    public function afterSave($object)
    {
        if ($object->getIsDuplicate() == true) {
            $this->duplicate($object);
            return;
        }

        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images']) || $object->isLockedAttribute($attrCode)) {
            return;
        }

        $storeId = $object->getStoreId();

        $storeIds = $object->getStoreIds();
        $storeIds[] = Mage_Core_Model_App::ADMIN_STORE_ID;

        // remove current storeId
        $storeIds = array_flip($storeIds);
        unset($storeIds[$storeId]);
        $storeIds = array_keys($storeIds);

        $images = Mage::getResourceModel('catalog/product')
            ->getAssignedImages($object, $storeIds);

        $picturesInOtherStores = array();
        foreach ($images as $image) {
            $picturesInOtherStores[$image['filepath']] = true;
        }

        $toDelete = array();
        $filesToValueIds = array();
        foreach ($value['images'] as &$image) {
            if(!empty($image['removed'])) {
                if(isset($image['value_id']) && !isset($picturesInOtherStores[$image['file']])) {
                    $toDelete[] = $image['value_id'];
                }
                continue;
            }

            if(!isset($image['value_id'])) {
                $data = array();
                $data['entity_id']      = $object->getId();
                $data['attribute_id']   = $this->getAttribute()->getId();
                $data['value']          = $image['file'];
                $image['value_id']      = $this->_getResource()->insertGallery($data);
            }

            $this->_getResource()->deleteGalleryValueInStore($image['value_id'], $object->getStoreId());

            // Add per store labels, position, disabled
            $data = array();
            $data['value_id'] = $image['value_id'];
            $data['label']    = $image['label'];
            $data['position'] = (int) $image['position'];
            $data['swatch'] = isset($image['swatch']) ? (int) $image['swatch'] : 0;

            // source $image['file'] old-style-name_matericode_color-code_sequence.jpg old-style-name_matericode_color-code_swatch.jpg
            // destnation $image['file'] old-style-name_color-code_sequence.jpg style-name_color-code_swatch.jpg
            $fileInfo = Mage::helper('gri_importdata')->getFileNameData($image['file']);
            if (!$data['swatch'] && count($fileInfo) == 3) {
                if ($color = $object->getResource()->getAttribute('color_code')->getSource()->getOptionId($fileInfo[1]))
                    $data['swatch'] = $color;
            }
            $data['disabled'] = (int) $image['disabled'];
            $data['store_id'] = (int) $object->getStoreId();

            $this->_getResource()->insertGalleryValueInStore($data);
        }

        $this->_getResource()->deleteGallery($toDelete);
    }

    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images'])) {
            return;
        }

        if(!is_array($value['images']) && strlen($value['images']) > 0) {
            $value['images'] = Mage::helper('core')->jsonDecode($value['images']);
        }

        if (!is_array($value['images'])) {
            $value['images'] = array();
        }

        $clearImages = array();
        $newImages   = array();
        $existImages = array();
        if ($object->getIsDuplicate()!=true) {
            foreach ($value['images'] as &$image) {
                if(!empty($image['removed'])) {
                    $clearImages[] = $image['file'];
                } else if (!isset($image['value_id'])) {
                    $newFile                   = $this->_moveImageFromTmp($image['file']);
                    $image['new_file'] = $newFile;
                    $newImages[$image['file']] = $image;
                    $this->_renamedImages[$image['file']] = $newFile;
                    $image['file']             = $newFile;
                } else {
                    $existImages[$image['file']] = $image;
                }
            }
        } else {
            // For duplicating we need copy original images.
            $duplicate = array();
            foreach ($value['images'] as &$image) {
                if (!isset($image['value_id'])) {
                    continue;
                }
                $duplicate[$image['value_id']] = $this->_copyImage($image['file']);
                $newImages[$image['file']] = $duplicate[$image['value_id']];
            }

            $value['duplicate'] = $duplicate;
        }

        foreach ($object->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $attrData = $object->getData($mediaAttrCode);

            if (in_array($attrData, $clearImages)) {
                $object->setData($mediaAttrCode, 'no_selection');
            }

            if (isset($newImages[$attrData]['new_file'])) {
                $object->setData($mediaAttrCode, $newImages[$attrData]['new_file']);
                $object->setData($mediaAttrCode.'_label', $newImages[$attrData]['label']);
            }

            if (isset($existImages[$attrData])) {
                $object->setData($mediaAttrCode.'_label', $existImages[$attrData]['label']);
            }
        }

        Mage::dispatchEvent('catalog_product_media_save_before', array('product' => $object, 'images' => $value));

        $object->setData($attrCode, $value);

        return $this;
    }
}
