<?php

class Gri_CatalogCustom_Block_Home_ShopNow extends Gri_CatalogCustom_Block_Category_Group
{
    protected $_template = 'catalog/home/shop_now.phtml';

    public function getCategories()
    {
        if ($this->getData('categories') === NULL) {
            $categories = parent::getCategories();
            $categories->addAttributeToSelect('thumbnail');
            $i = 0;
            if ($labels = explode(',', $this->getLabels())) foreach ($categories as $category) {
                isset($labels[$i]) and $category->setName($labels[$i]);
                $category->setImage($category->getThumbnail());
                ++$i;
            }
            $this->setData('categories', $categories);
        }
        return $this->getData('categories');
    }
}
