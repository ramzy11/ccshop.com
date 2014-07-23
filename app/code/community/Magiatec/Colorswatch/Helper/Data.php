<?php
class Magiatec_Colorswatch_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getAllowedExtensions()
    {
        return array('jpg', 'jpeg', 'gif', 'png');
    }

    public function getSwatchesFile($filename = '')
    {
        return Mage::getBaseDir('media') . DS . 'colorswatch' . DS . $filename;
    }
    public function getSwatchesPath($filename = '')
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                . 'colorswatch/' . $filename;
    }

    public function getSwatchPath($swatch)
    {
        if (is_string($swatch)) {
            $filename = $swatch;
        }
        elseif ($swatch instanceof Varien_Object) {
            $filename = $swatch->getImage();
        }
        else {
            return '';
        }

        $width  = Mage::getStoreConfig('magiatecolorswatch/settings/swatch_width');
        $height = Mage::getStoreConfig('magiatecolorswatch/settings/swatch_height');

        if (!$width OR !$height) {
            return $this->getSwatchesPath($filename);
        }

        $resizedFolder = sprintf('%dx%d',$width, $height);

        if (file_exists($source = $this->getSwatchesFile($filename)) && (
            !file_exists($destination = $this->getSwatchesFile($resizedFolder.DS.$filename)) ||
            filemtime($source) > filemtime($destination)
        )) {
            if (!file_exists($this->getSwatchesFile($resizedFolder))) {
                @mkdir($this->getSwatchesFile($resizedFolder), 0777);
            }
            try {
                if (is_file($destination)) unlink($destination);
                $imageObj = new Varien_Image($source);
                $imageObj->constrainOnly(true);
                $imageObj->keepFrame(false);
                $imageObj->keepTransparency(true);
                $imageObj->resize($width, $height);
                $imageObj->save($destination);
            }
            catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'magiatecolorswatch.log');
            }
        }

        return $this->getSwatchesPath($resizedFolder.'/'.$filename);
    }

    public function switchTemplateIf($name, $block)
    {
        if (Mage::getStoreConfig('magiatecolorswatch/settings/enabled') AND
                Mage::getStoreConfig('magiatecolorswatch/imageswitcher/enabled')) {
            return $name;
        }

        if ($blockObject = Mage::getSingleton('core/layout')->getBlock($block)) {
            return $blockObject->getTemplate();
        }

        return '';
    }

    public function loadJquery($zoomjs)
    {
        //$protocol = Mage::getStoreConfig('web/secure/use_in_frontend') ? 'https' : 'http';
        return /*'<script type="text/javascript" src="'.$protocol.'://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript">jQuery.noConflict();</script>*/
        '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).$zoomjs.'"></script>';
    }

    public function getZoomOptions()
    {
        $data = array(
            'zoomType' => Mage::getStoreConfig('magiatecolorswatch/zoom/type'),
            'zoomWidth' => (int) Mage::getStoreConfig('magiatecolorswatch/zoom/width'),
            'zoomHeight' => (int) Mage::getStoreConfig('magiatecolorswatch/zoom/height'),
            'preloadText' => Mage::getStoreConfig('magiatecolorswatch/zoom/preload_text'),
            'position' => Mage::getStoreConfig('magiatecolorswatch/zoom/position'),
            'title' => (int) Mage::getStoreConfig('magiatecolorswatch/zoom/title'),
            'imageOpacity' => (int) Mage::getStoreConfig('magiatecolorswatch/zoom/opacity'),
        );
        return Mage::helper('core')->jsonEncode($data);
    }
}
