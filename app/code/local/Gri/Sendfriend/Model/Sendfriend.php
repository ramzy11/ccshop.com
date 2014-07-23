<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Sendfriend
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SendFriend Log
 *
 * @method Mage_Sendfriend_Model_Resource_Sendfriend _getResource()
 * @method Mage_Sendfriend_Model_Resource_Sendfriend getResource()
 * @method int getIp()
 * @method Mage_Sendfriend_Model_Sendfriend setIp(int $value)
 * @method int getTime()
 * @method Mage_Sendfriend_Model_Sendfriend setTime(int $value)
 *
 * @category    Gri
 * @package     Gri_Sendfriend
 * @author      Jack Yu @ GRI <jack_yu@griretail.com>
 */

class Gri_Sendfriend_Model_Sendfriend extends Mage_Sendfriend_Model_Sendfriend
{
    public function send()
    {
        if ($this->isExceedLimit()){
            Mage::throwException(Mage::helper('sendfriend')->__('You have exceeded limit of %d sends in an hour', $this->getMaxSendsToFriend()));
        }

        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');

        $message = nl2br(htmlspecialchars($this->getSender()->getMessage()));
        $sender  = array(
            'name'  => $this->_getHelper()->htmlEscape($this->getSender()->getName()),
            'email' => $this->_getHelper()->htmlEscape($this->getSender()->getEmail())
        );

        $mailTemplate->setDesignConfig(array(
            'area'  => 'frontend',
            'store' => Mage::app()->getStore()->getId()
        ));

		/** added by Jack **/
		$_price = Mage::helper('tax')->getPrice($this->getProduct(), $this->getProduct()->getPrice());
		$product_brand = htmlspecialchars($this->getProduct()->getAttributeText('brand'));
		$product_price = Mage::helper('core')->currency($_price,true,false);
		/** end added by Jack **/
		
        foreach ($this->getRecipients()->getEmails() as $k => $email) {
            $name = $this->getRecipients()->getNames($k);
            $mailTemplate->sendTransactional(
                $this->getTemplate(),
                $sender,
                $email,
                $name,
                array(
                    'name'          => $name,
                    'email'         => $email,
                    'product_name'  => $this->getProduct()->getName(),
                    'product_url'   => $this->getProduct()->getUrlInStore(),
                    'message'       => $message,
                    'sender_name'   => $sender['name'],
                    'sender_email'  => $sender['email'],
                    'product_image' => Mage::helper('catalog/image')->init($this->getProduct(),'small_image')->resize(75),
					/**added by Jack **/
					'product_brand' => $product_brand,
					'product_price' => $product_price,
					'product_image_tag' => "<img src='".Mage::helper('catalog/image')->init($this->getProduct(),'small_image')->resize(95)."' border='0' width='95' />",
					/**end added by Jack **/
                )
            );
        }

        $translate->setTranslateInline(true);
        $this->_incrementSentCount();

        return $this;
    }
}
