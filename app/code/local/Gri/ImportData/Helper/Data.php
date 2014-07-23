<?php
class Gri_ImportData_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_CREATE_INVENTORY = 'cataloginventory/import/create';
    const CONFIG_PATH_UPDATE_INVENTORY = 'cataloginventory/import/update';

    protected $_brandCategory = array();

    /**
     * field and attribute code mapping
     * @return array
     */
    public function importAttributesMap()
    {
        return array(
            'Color Code' => 'color_code',
            'Style Name' => 'style_name',
            'Original Price' => 'price',
            'Discount Price' => 'special_price',
            'Product Name' => 'name',
            'SKU' => 'sku',
            'Attribute Set' => 'attribute_set',
            'Product Description' => 'description',
            'Brand' => 'brand',
            'Qty' => 'qty',
            'Wear This With' => 'related',
            'Category1' => 'category1',
            'Category2' => 'category2',
            'Category3' => 'category3',
            'Configurable' => 'configurable',
            'Style NO.' => 'style_no',
            'Video Url' => 'video_url',
            'Video Url CN' => 'video_url_cn',
            'Search Keyword' => 'keywords',
            'Heel Height' => 'heel_height',
            'Weight (kg)' => 'weight',
            'Search Priority' => 'priority',
            'Best Seller' => 'best_seller',
            'Editor\'s Pick' => 'editors_pick',
            'Season' => 'season',
            'Color Filter 1' => 'color_filter_1',
            'Color Filter 2' => 'color_filter_2',
            'Size Filter' => 'size_filter',
            'Shoes Size' => 'size_shoes',
            'Clothing Size' => 'size_clothing',
            'Color Label' => 'color_label',
            'Country Group' => 'country_group',
            'Size & Fit' => 'size_n_fit',
            'Delivery & Free Returns' => 'delivery_n_returns',
            'Fitting Report' => 'fitting_report',
            'Material' => 'material',
            'Country of Manufacture' => 'country_of_manufacture',
            'Insole Material' => 'insole_material',
            'Sole Material' => 'sole_material',
            'Calf Circumference' => 'calf_circumference',
            'Boot Shaft Height' => 'boot_shaft',
            'Platform Height' => 'platform_height',
            'Toe Box Shape' => 'toe_box_shape',
            'Lining Material' => 'lining_material',
            'Lining with Fur' => 'lining_with_fur',
            'Trend' => 'trend',
            'Occasion' => 'occasion',
            'Length (cm)' => 'length',
            'Bust (cm)' => 'bust',
            'Sleeve Length (cm)' => 'sleeve_length',
            'Shoulder (cm)' => 'shoulder',
            'Waist (cm)' => 'waist',
            'Hip (cm)' => 'hip',
            'Thigh Circumference (cm)' => 'thigh_circumference',
            'Pant Length (cm)' => 'pant_length',
            'Dress length (cm)' => 'dress_length',
            'Front' => 'front',
            'Sleeve' => 'sleeve',
            'Collar' => 'collar',
            'With Lining' => 'with_lining',
            'With Accessories' => 'with_accessories',
            'Width (cm)' => 'width',
            'Height (cm)' => 'height',
            'Handle Drop (cm)' => 'handle_drop',
            'Strap Length (cm)' => 'strap_length',
            'Internal structure' => 'internal_structure',
            'Closure' => 'closure',
            'Set as New from' => 'news_from_date',
            'Set as New to' => 'news_to_date',
            'Pre-order from' => 'preorder_from_date',
            'Pre-order to' => 'preorder_to_date',
            'Discount %'=> 'discount',
            'Ref. No.'=> 'ref_no',
            'Limited Edition'=> 'limited_edition',
            'Store'=> 'store',
            'Sorting'=> 'sorting',
            'Status'=> 'status',
            'Additional Accessories'=> 'additional_accessories',
            'With Wash Instructions'=> 'with_wash_instructions',
            'Is Archived'=>'is_archived',
            'Sole with Fur' => 'sole_with_fur'
        );
    }

    public function defaultAttributeValue()
    {
        return array(
            'type' => 'simple',
            'status' => 'Enabled',
            'visibility' => 'Catalog, Search',
            'store' => 'admin',
            'websites' => 'base',
            'is_in_stock' => 1,
            'tax_class_id' => 'none',
            'discount'=> 0,
            'ref_no'=> '-',
            'limited_edition'=> 0
        );
    }

    public function canCreateInventory()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_CREATE_INVENTORY);
    }

    public function canUpdateInventory()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_UPDATE_INVENTORY);
    }

    public function getAttributeData()
    {
        return array();
    }

    public function getConfigAttributeIds($attribute_set = '')
    {
        $attributes = array('color_code');
        if (strtolower($attribute_set) == 'shoes') $attributes[] = 'size_shoes';
        if (strtolower($attribute_set) == 'clothing') $attributes[] = 'size_clothing';
        $ids = array();
        foreach ($attributes as $v)
            if ($id = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product', $v)) $ids[] = $id;
        return $ids;
    }

    public function getShopCategory()
    {
        return Mage::getModel('catalog/category')->loadByAttribute('name', 'shop');
    }

    public function getBrandCategory($brandName)
    {
        if (!isset($this->_brandCategory[$brandName])) {
            $this->_brandCategory[$brandName] = Mage::getModel('catalog/category')->loadByAttribute('name', $brandName);
        }
        return $this->_brandCategory[$brandName];
    }

    public function unzipImages($package, $destination)
    {
        if (!is_file($package)) return '';
        $zip = new ZipArchive;
        $res = $zip->open($package);
        if ($res === TRUE) {
            $zip->extractTo($destination);
            $zip->close();
            unlink($package);
        } else {
            $message = Mage::helper('catalog')->__('Unzip image fail.', 'store');
            Mage::throwException($message);
        }
    }

    /**
     * @param $filename
     * @return array
     *
     *   array(
     *       'style_name',
     *       'color_code',
     *       'sequense' or  swatch
     *   )
     */
    public function getFileNameData($filename ,$separator = '_'){
        $fileNameData = explode($separator, $filename);
        $ret = $fileNameData;
        if(count($fileNameData) >=4 ){
            $ret = array(0=>'', 1=>'', 2=>'');
            $fileNameData = array_reverse($fileNameData);

            $ret[1] = $fileNameData[1]; // color_code
            $ret[2] = $fileNameData[0]; // sequence /swatch
            unset($fileNameData[0],$fileNameData[1]);
            $fileNameData = array_reverse($fileNameData);
            foreach($fileNameData as $k =>$v){
                $ret[0] .= $v.$separator;
            }

            // style_name
            $ret[0] = substr($ret[0], 0, strlen($ret[0])-1);
        }

        return  $ret ;
    }

}
