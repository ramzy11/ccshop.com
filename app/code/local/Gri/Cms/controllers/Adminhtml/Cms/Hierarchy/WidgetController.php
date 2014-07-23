<?php



/**
 * Admihtml Widget Controller for Hierarchy Node Link plugin
 *
 * @category   Gri
 * @package    Gri_Cms
 */
class Gri_Cms_Adminhtml_Cms_Hierarchy_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $this->getResponse()->setBody(
            $this->_getTreeBlock()
                ->setScope($this->getRequest()->getParam('scope'))
                ->setScopeId((int)$this->getRequest()->getParam('scope_id'))
                ->getTreeHtml()
        );
    }

    /**
     * Tree block instance
     *
     * @return Gri_Cms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser
     */
    protected function _getTreeBlock()
    {
        return $this->getLayout()->createBlock('gri_cms/adminhtml_cms_hierarchy_widget_chooser', '', array(
            'id' => $this->getRequest()->getParam('uniq_id')
        ));
    }
}
