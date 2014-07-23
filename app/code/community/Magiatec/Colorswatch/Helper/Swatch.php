<?php

class Magiatec_Colorswatch_Helper_Swatch extends Mage_Core_Helper_Abstract
{
    /**
     * @var Mage_Adminhtml_Block_Html_Select
     */
    protected $_colorSelect;
    /**
     * @var Magiatec_Colorswatch_Block_Product_View_Type_Configurable
     */
    protected $_configurableBlock;
    protected $_currentProduct;
    protected $_imageSwatchSelect;
    protected $_isApplicable;
    /**
     * @var Magiatec_Colorswatch_Model_Product_Attribute_Backend_Media
     */
    protected $_mediaBackend;
    protected $_productAttributes;
    protected $_swatchAttributeIds;

    public function associateSwatchPictures(Varien_Object $object, $key = 'value')
    {
        $value = $object->getData($key);
        $imageIds = array();
        if (isset($value['images'])) foreach ($value['images'] as $k => $image) {
            $imageIds[$k] = $image['value_id'];
        }
        foreach ($this->loadImageSwatches($imageIds) as $k => $swatch) {
            $value['images'][$k]['swatch'] = $swatch;
        }
        $object->setData($key, $value);
        return $this;
    }

    public function checkAttributes($attrId = null)
    {
        $attributeIds = $this->getSwatchAttributeIds();
        if ($attrId) {
            return in_array($attrId, $attributeIds);
        }

        foreach ($this->getProductAttributes() as $attr) {
            if (in_array($attr->getAttributeId(), $attributeIds)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getConfigurableBlock()
    {
        return $this->_configurableBlock;
    }

    public function getImagePosByFileName($path,$sp = '_'){
        $fileData = pathinfo($path);
        $fileNameData = explode($sp, strtolower($fileData['filename']));
        if(count($fileNameData) > 3 || !isset($fileNameData[2])) return false;

        return (int)$fileNameData[2];
    }


    public function getImageByFileNameSeq($images,$attributeSet = NULL ,$position = 2){

    }

    public function getAttrSetNameById($attrSetId){
        return Mage::getSingleton('eav/entity_attribute_set')->load($attrSetId)->getAttributeSetName();
    }

    public function getConfigurableOptions(Mage_Catalog_Model_Product $product)
    {
        $this->_configurableBlock or
            $this->_configurableBlock = Mage::app()->getLayout()
                ->createBlock('catalog/product_view_type_configurable')
        ;
        $this->_mediaBackend or
            $this->_mediaBackend = Mage::getModel('catalog/product_attribute_backend_media')
                ->setAttribute($product->getResource()->getAttribute('media_gallery'))
        ;
        $this->_configurableBlock->setProduct($product);
        $config = Mage::helper('core')->jsonDecode($this->_configurableBlock
            ->getJsonConfig());

        //$attrSetName = $this->getAttrSetNameById($product->getAttributeSetId());
        foreach ($config['attributes'] as $attribute) {
            if ($this->checkAttributes($attribute['id'])) {
                $this->_mediaBackend->afterLoad($product);
                $options = $attribute['options'];
                $gallery = $product->getMediaGalleryImages();

                $selectImages = array();
                $mainImages = array();
                $secondImages = array();
                $count = 0;

                foreach ($gallery as $v) {
                    //if ($v->getDisabled() || isset($mainImages[$v->getSwatch()])) continue;
                    //get 2 images in a color
                    if($v->getDisabled() || isset($selectImages[$v->getSwatch()])){
                        if(count($selectImages[$v->getSwatch()]) >= 2){
                            $count = 0;
                            continue;
                        }
                    }
                    $selectImages[$v->getSwatch()][$count] = $v->getFile();

                    if(isset($selectImages[$v->getSwatch()][0]) && $v->getSwatch() != 0 ) $mainImages[$v->getSwatch()] = $selectImages[$v->getSwatch()][0];
                    if(isset($selectImages[$v->getSwatch()][1]) && $v->getSwatch() != 0 ) $secondImages[$v->getSwatch()] = $selectImages[$v->getSwatch()][1];
                    $count ++;
                }



                foreach ($options as $k => $v) {
                    $options[$k]['attribute_id'] = $attribute['id'];
                    $options[$k]['main_image'] = isset($mainImages[$v['id']]) ? $mainImages[$v['id']] : '';
                    $options[$k]['second_image'] = isset($secondImages[$v['id']]) ? $secondImages[$v['id']] : '';
                }

                return $options;
            }
        }
        return array();
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getCurrentProduct()
    {
        if ($this->_currentProduct === NULL) {
            $this->_currentProduct = Mage::registry('current_product');
        }
        return $this->_currentProduct;
    }

    public function getImageSwatchSelect()
    {
        return $this->_imageSwatchSelect;
    }

    public function getProductAttributes()
    {
        if ($this->_productAttributes === NULL) {
            $_product = $this->getCurrentProduct();
            $this->_productAttributes = $_product->getTypeInstance()
                ->getConfigurableAttributes($_product);
        }
        return $this->_productAttributes;
    }

    public function getSwatchAttributeIds()
    {
        if ($this->_swatchAttributeIds === NULL) {
            $attributeIds = Mage::getStoreConfig('magiatecolorswatch/settings/attributes');
            $attributeIds = explode(',', $attributeIds);
            $this->_swatchAttributeIds = $attributeIds;
        }
        return $this->_swatchAttributeIds;
    }

    /**
     * @return Mage_Adminhtml_Block_Html_Select
     */
    public function getSwatchDropDown()
    {
        if ($this->_colorSelect === NULL) {
            $this->_colorSelect = Mage::app()->getLayout()->createBlock('adminhtml/html_select');
            foreach($this->getProductAttributes() as $attribute) {
                if (!$this->checkAttributes($attribute->getAttributeId())) continue;
                if ($prices = $attribute->getPrices()) {
                    $this->_colorSelect->addOption(0, '');
                    foreach ($prices as $value) {
                        $this->_colorSelect->addOption($value['value_index'], $value['store_label']);
                    }
                    break;
                }
            }
        }
        return $this->_colorSelect;
    }

    public function getSwatchUrl($productUrl, $option)
    {
        if (!$productUrl) return 'javascript:;';
        $productUrl .= '#' . $option['attribute_id'] . '=' . $option['id'];
        return $productUrl;
    }

    public function isApplicable()
    {
        if ($this->_isApplicable === NULL) {

           $types = array( Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE );
           $modules = (array)Mage::getConfig()->getNode('modules')->children();

           if(isset($modules['ProxiBlue_GiftPromo'])) {
                $types[] = ProxiBlue_GiftPromo_Model_Product_Type_Gift_Configurable::TYPE_CODE;
            }

           $this->_isApplicable = in_array( $this->getCurrentProduct()->getTypeId() ,
                                            $types
                                          )
                                  && $this->checkAttributes();
        }

        return $this->_isApplicable;
    }

    public function loadImageSwatches(array $imageIds)
    {
        $result = array();
        if ($imageIds) {
            $product = $this->getCurrentProduct();
            /* @var $read Varien_Db_Adapter_Pdo_Mysql */
            $read = $product->getResource()->getReadConnection();

            if (!$select = $this->getImageSwatchSelect()) {
                $this->setImageSwatchSelect($select = $read->select()->from(
                    array('main' => $product->getResource()
                        ->getTable('catalog/product_attribute_media_gallery_value')),
                    array('value_id', 'swatch')
                ));
            }
            $select->reset('where')->where('main.value_id IN (?)', $imageIds);

            $imageIdKeys = array_flip($imageIds);
            foreach($read->fetchAll($select) as $v) {
                $result[$imageIdKeys[$v['value_id']]] = $v['swatch'];
            }
        }
        return $result;
    }

    public function rearrangeGalleryImages($galleryImages)
    {
        $result = array();
        foreach ($galleryImages as $image) {
            $result[$image->getSwatch()][] = $image;
        }
        return $result;
    }

    public function resetApplicable()
    {
        $this->_isApplicable = NULL;
        return $this;
    }

    public function resetColorSelect()
    {
        $this->_colorSelect = NULL;
        return $this;
    }

    public function resetProductAttributes()
    {
        $this->_productAttributes = NULL;
        return $this;
    }

    public function setCurrentProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_currentProduct = $product;
        $this->resetApplicable()->resetColorSelect()->resetProductAttributes();
        return $this;
    }

    public function setImageSwatchSelect(Varien_Db_Select $select)
    {
        $this->_imageSwatchSelect = $select;
        return $this;
    }

    public function getFirstColorSwatchImage($product)
    {
        $this->setCurrentProduct($product);
        $image = NULL;
        if ($this->isApplicable()) {
            $options = $this->getConfigurableOptions($product);
            ($firstOption = array_shift($options)) && $image = $firstOption['main_image'];
        }

        return $image;
    }
}
