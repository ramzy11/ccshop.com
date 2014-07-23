<?php

class Gri_Core_Model_System_Currencysymbol extends Mage_CurrencySymbol_Model_System_Currencysymbol
{

    public function setCurrencySymbolsData($symbols = array())
    {
        $symbols = array_filter($symbols);
        if ($symbols) {
            $value['options']['fields']['customsymbol']['value'] = serialize($symbols);
        } else {
            $value['options']['fields']['customsymbol']['inherit'] = 1;
        }

        Mage::getModel('adminhtml/config_data')
            ->setSection(self::CONFIG_SECTION)
            ->setWebsite(NULL)
            ->setStore(NULL)
            ->setGroups($value)
            ->save();

        Mage::dispatchEvent('admin_system_config_changed_section_currency_before_reinit',
            array('website' => $this->_websiteId, 'store' => $this->_storeId)
        );

        // re-init configuration
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        $this->clearCache();

        Mage::dispatchEvent('admin_system_config_changed_section_currency',
            array('website' => $this->_websiteId, 'store' => $this->_storeId)
        );

        return $this;
    }
}
