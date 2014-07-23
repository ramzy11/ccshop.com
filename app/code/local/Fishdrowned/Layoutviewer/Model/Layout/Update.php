<?php
/**
 *
 * @author alanstorm@alanstorm.com
 * @link http://alanstorm.com/layouts_blocks_and_templates
 * @see Mage_Core_Model_Layout_Update
 *
 */

class Fishdrowned_Layoutviewer_Model_Layout_Update extends Mage_Core_Model_Layout_Update
{
    // we'll display this request's package layout
    // we'll also display this request's "reduced" layout, etc. etc

    public function getPackageLayout()
    {
        $this->fetchFileLayoutUpdates();
        return $this->_packageLayout;
    }
}