<?php

class Gri_ColorFilter_Helper_Data extends Mage_Core_Helper_Abstract
{
    CONST CONFIG_PATH_COLOR_FILTER_ENABLED = 'colorfilter/settings/enabled';
    CONST CONFIG_PATH_COLOR_FILTER_IMAGE_WIDTH = 'colorfilter/image/width';
    CONST CONFIG_PATH_COLOR_FILTER_IMAGE_HEIGHT = 'colorfilter/image/height';

    public function isEnabled(){
        return (bool)Mage::getStoreConfig(self::CONFIG_PATH_COLOR_FILTER_ENABLED);
    }

    public function getImagePath($filename = '')
    {
        return Mage::getBaseDir('media') . DS . 'color_filter' . DS . str_replace('/', DS, $filename);
    }

    public function getImageUrl($filename = '', $absolute = TRUE)
    {
        $url = 'color_filter'.DS .$filename;
        $absolute and $url = Mage::getBaseUrl('media') . $url;
        return $url;
    }

    public function getImageWidth()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_COLOR_FILTER_IMAGE_WIDTH);
    }

    public function getImageHeight()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_COLOR_FILTER_IMAGE_HEIGHT);
    }

    /**
     * @return Gri_ColorFilter_Model_ColorFilter
     */
    public function getActiveColor()
    {
        if (!$colorFilter = Mage::registry('color_filter')) {
            if (Mage::getStoreConfig(self::CONFIG_PATH_COLOR_FILTER_ENABLED)) {
                /* @var $colorFilter Gri_ColorFilter_Model_ColorFilter */
                $colorFilter = Mage::getModel('gri_colorfilter/colorFilter');
                $colorFilter->loadActiveColor();
            }
            else $colorFilter = FALSE;
            Mage::register('color_filter', $colorFilter);
        }

        return $colorFilter;
    }

    public function getStoreImages($images,$store_code){
        foreach($images as $k => $value){
            if(isset($value['label'])){
                $storeLabelData = $value['label'];
                $tmp = explode(',',$storeLabelData);
                if($store_code == 'hk_eng')
                    if(isset($tmp[0]))
                        $images[$k]['label'] = $tmp[0];

                if($store_code == 'hk_cht')
                    isset($tmp[1]) ? $images[$k]['label'] = $tmp[1]:$images[$k]['label'] = $tmp[0];

            }
        }

        return $images;
    }
}
