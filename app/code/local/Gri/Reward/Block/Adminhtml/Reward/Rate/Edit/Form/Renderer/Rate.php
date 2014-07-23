<?php
/**
 * Magento Gri Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Gri Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/gri-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Gri
 * @package     Gri_Reward
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */


/**
 * Reward rate form field (element) renderer
 *
 * @category    Gri
 * @package     Gri_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Reward_Block_Adminhtml_Reward_Rate_Edit_Form_Renderer_Rate
    extends Mage_Adminhtml_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->setTemplate('gri/reward/rate/form/renderer/rate.phtml');
    }

    /**
     * Return HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Getter
     * Return value index in element object
     *
     * @return string
     */
    public function getValueIndex()
    {
        return $this->getElement()->getValueIndex();
    }

    /**
     * Getter
     * Return value by given value index in element object
     *
     * @return float | integer
     */
    public function getValue()
    {
        return $this->getRate()->getData($this->getValueIndex());
    }

    /**
     * Getter
     * Return equal value index in element object
     *
     * @return string
     */
    public function getEqualValueIndex()
    {
        return $this->getElement()->getEqualValueIndex();
    }

    /**
     * Return value by given equal value index in element object
     *
     * @return float | integer
     */
    public function getEqualValue()
    {
        return $this->getRate()->getData($this->getEqualValueIndex());
    }
}
