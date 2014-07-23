<?php

class Gri_Page_Block_Html_TopmenuV2014 extends Mage_Page_Block_Html_Topmenu
{
    protected $_flag = array();

    public function getCodeByUrl($url){
        if(substr($url,0,1) == '#'){ //if #xxxx => xxxx
            return substr($url,1,strlen($url)-1);
        }
        return basename($url,'.html');// http://www..../xxx.html => xxx
    }

    public function filterUrl($url){
        if(substr($url,0,1) == '#') return 'javascript:void(0);';
        return $url;
    }

    protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();


            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }


            //Add All Link
            $categoriesNamesIncludeMenus = Mage::helper('gri_catalogcustom')->getStoreShop();
            if( $child->getLevel() == 1 ) {
                $parent = $child->getParent();
                if((!isset($this->_flag[$parent->getId()]) || !$this->_flag[$parent->getId()])
                          && in_array(strtolower($this->getCodeByUrl($parent->getUrl())),$categoriesNamesIncludeMenus)){
                    $html .= '<li class="level1">
                          <a href="'.$parent->getUrl().'"><span>'.$this->__('All').'</span></a></li>';
                    $this->_flag[$parent->getId()] = true;
                }
            }

            $menuUrl = $this->filterUrl($child->getUrl());
            if ($childLevel == 0 && strstr($this->_getRenderedMenuItemAttributes($child),'parent')){
                $menuUrl = 'javascript:void(0)';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            //$html .= '<a href="' . $this->filterUrl($child->getUrl()) . '" ' . $outermostClassCode . '><span>'
            $html .= '<a href="' . $menuUrl . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName()) . '</span></a>';

            if ($child->hasChildren()) {
                $code = $this->getCodeByUrl($child->getUrl());
                $cmsIdentifier = strtolower($code);
                $menuCmsTitleIdentifier = $cmsIdentifier.'_menu_title';
                $menuCmsContentIdentifier = $cmsIdentifier.'_menu_content';
                if (!empty($childrenWrapClass)) {
                    $html .= '<div class="' . $childrenWrapClass . '">';
                }
                $html .= '<div class="menu-back">'.$this->__('Back').'</div>';
                $html .= '<div class="shop-by-category">';
                if(Mage::helper('gri_page')->getCmsBlockHtml($menuCmsTitleIdentifier))
                    $html .= Mage::helper('gri_page')->getCmsBlockHtml($menuCmsTitleIdentifier);
                $html .= '<ul class="level' . $childLevel . '">';
                $html .= $this->_getHtml($child, $childrenWrapClass);
                $html .= '</ul>';
                $html .= '</div>';

                if ($childLevel == 0 && $outermostClass){
                    if(Mage::helper('gri_page')->getCmsBlockHtml($menuCmsTitleIdentifier))
                        $html .= Mage::helper('gri_page')->getCmsBlockHtml($menuCmsContentIdentifier);
                }

                if (!empty($childrenWrapClass)) {
                    $html .= '</div>';
                }
            }

            $html .= '</li>';

            $counter++;
        }

        return $html;
    }
}






