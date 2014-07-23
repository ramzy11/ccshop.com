<?php

/**
 * Cron functions
 *
 * @category   ProxiBlue
 * @package    ProxiBlue_DynCatProd
 * @author     Lucas van Staden (support@proxiblue.com.au)
 */
class ProxiBlue_DynCatProd_Model_Cron {

    /**
     * Rebuild the indexes nightly to allow date ranges index prodicts to work correctly
     * 
     * @param object $schedule
     * @return mixed
     */
    public static function rebuildIndex($schedule) {
        try {
            require_once Mage::getBaseDir().DS.'app'.DS.'Mage.php';
            umask( 0 );
            Mage :: app( "default" );
 
            $indexingProcesses = Mage::getSingleton('index/indexer')->getProcessesCollection(); 
            foreach ($indexingProcesses as $process) {
                if($process->getIndexerCode() == 'catalog_category_dynamic_product'){
                    $process->reindexEverything();
                }    
            }
            return true;
        } catch (Dhmedia_Exception $e) {
            Mage::logException($e);
            return $e->getMessage();
        }
    }
   
}

