<?php



/**
 * Cms Hierarchy Model for config processing
 *
 * @category   Gri
 * @package    Gri_Cms
 */
class Gri_Cms_Model_Hierarchy_Config
{
    const XML_PATH_CONTEXT_MENU_LAYOUTS = 'global/gri_cms/hierarchy/menu/layouts';

    /**
     * Menu layouts configuration
     * @var array
     */
    protected $_contextMenuLayouts = null;

    /**
     * Defalt code for menu layouts
     * @var string
     */
    protected $_defaultMenuLayoutCode;

    /**
     * Initialization for $_contextMenuLayouts
     *
     * @return Gri_Cms_Model_Hierarchy_Config
     */
    protected function _initContextMenuLayouts()
    {
        $config = Mage::getConfig()->getNode(self::XML_PATH_CONTEXT_MENU_LAYOUTS);
        if ($this->_contextMenuLayouts !== null || !$config) {
            return $this;
        }
        if (!is_array($this->_contextMenuLayouts)) {
            $this->_contextMenuLayouts = array();
        }
        foreach ($config->children() as $layoutCode => $layoutConfig) {
            $this->_contextMenuLayouts[$layoutCode] = new Varien_Object(array(
                'label'                 => Mage::helper('gri_cms')->__((string)$layoutConfig->label),
                'code'                  => $layoutCode,
                'layout_handle'         => (string)$layoutConfig->layout_handle,
                'is_default'            => (int)$layoutConfig->is_default,
                'page_layout_handles'   => (array)$layoutConfig->page_layout_handles,
            ));
            if ((bool)$layoutConfig->is_default) {
                $this->_defaultMenuLayoutCode = $layoutCode;
            }
        }
        return $this;
    }

    /**
     * Return available Context Menu layouts output
     *
     * @return array
     */
    public function getContextMenuLayouts()
    {
        $this->_initContextMenuLayouts();
        return $this->_contextMenuLayouts;
    }

    /**
     * Return Context Menu layout by its code
     *
     * @param string $layoutCode
     * @return Varien_Object|boolean
     */
    public function getContextMenuLayout($layoutCode)
    {
        $this->_initContextMenuLayouts();
        return isset($this->_contextMenuLayouts[$layoutCode]) ? $this->_contextMenuLayouts[$layoutCode] : false;
    }

    /**
     * Getter for $_defaultMenuLayoutCode
     *
     * @return string
     */
    public function getDefaultMenuLayoutCode()
    {
        $this->_initContextMenuLayouts();
        return $this->_defaultMenuLayoutCode;
    }
}
