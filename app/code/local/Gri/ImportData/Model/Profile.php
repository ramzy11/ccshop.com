<?php

class Gri_ImportData_Model_Profile extends Mage_Dataflow_Model_Profile {

    protected function _afterSave() {
        if (!$this->getGuiData()) {
            if (isset($_FILES['file_1']['tmp_name'])) {
                for ($index = 0; $index < 1; $index++) {
                    if ($file = $_FILES['file_' . ($index + 1)]['tmp_name']) {
                        $uploader = new Mage_Core_Model_File_Uploader('file_' . ($index + 1));
                        $uploader->setAllowedExtensions(array('csv', 'xml'));
                        $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
                        $uploader->save($path);
                        $newFilename = $uploader->getUploadedFileName();
                    }
                    //BOM deleting for UTF files
                    if (isset($newFilename) && $newFilename) {
                        $contents = file_get_contents($path . $newFilename);
                        if (ord($contents[0]) == 0xEF && ord($contents[1]) == 0xBB && ord($contents[2]) == 0xBF) {
                            $contents = substr($contents, 3);
                            file_put_contents($path . $newFilename, $contents);
                        }
                        unset($contents);
                    }
                }
            }
            unset($_FILES['file_1']);
        }
        parent::_afterSave();
    }

}