<?php

require_once ('app/code/core/Mage/Adminhtml/controllers/System/Convert/ProfileController.php');

class Gri_ImportData_Adminhtml_System_Convert_ProfileController extends Mage_Adminhtml_System_Convert_ProfileController
{
    protected $_imageFiles = array();
    protected $_ioAdapter;

    public function appendImage(Mage_Catalog_Model_Product $product, $data)
    {
        /* @var $mediaGalleryBackendModel Magiatec_Colorswatch_Model_Product_Attribute_Backend_Media */
        $mediaGalleryBackendModel = $product->getResource()->getAttribute('media_gallery')->getBackend();
        $arrayToMassAdd = array();
        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($data[$mediaAttributeCode])) {
                $file = trim($data[$mediaAttributeCode]);
                if (!empty($file) && $file != 'no_selection' && !in_array($file, $product->getExistingImages())) {
                    $arrayToMassAdd[$mediaAttributeCode] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                }
            }
        }
        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product, $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import' . DS, true, false
        );
        $galleryData = explode(',', $data['gallery']);
        foreach ($galleryData as $gallery_img) {
            try {
                $product->addImageToMediaGallery($filename = Mage::getBaseDir('media') . DS . 'import' . DS . $gallery_img, null, true, false);
            } catch (Exception $e) {
                Mage::log($filename . ': ' . $e->getMessage(), NULL, 'product.import.log');
            }
        }
        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);
        $product->save();
    }

    public function batchFinishAction()
    {
        $batchId = $this->getRequest()->getParam('id');
        $this->saveRelation();
        $this->saveImage();
        if ($batchId) {
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);
            /* @var $batchModel Mage_Dataflow_Model_Batch */

            if ($batchModel->getId()) {

                $result = array();
                try {
                    $batchModel->beforeFinish();
                    $this->removeFile();
                } catch (Mage_Core_Exception $e) {
                    $result['error'] = $e->getMessage();
                } catch (Exception $e) {
                    $result['error'] = Mage::helper('adminhtml')->__('An error occurred while finishing process. Please refresh the cache');
                }
                $batchModel->delete();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }

    public function batchRunAction()
    {
        Mage::register('skip_entity_indexer', TRUE);
        Mage::register('skip_swatch_sort', TRUE);
        if ($this->getRequest()->isPost()) {
            $batchId = $this->getRequest()->getPost('batch_id', 0);
            $rowIds = $this->getRequest()->getPost('rows');

            /* @var $batchModel Mage_Dataflow_Model_Batch */
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);

            if (!$batchModel->getId()) {
                return;
            }
            if (!is_array($rowIds) || count($rowIds) < 1) {
                return;
            }
            if (!$batchModel->getAdapter()) {
                return;
            }

            $batchImportModel = $batchModel->getBatchImportModel();
            $importIds = $batchImportModel->getIdCollection();

            $adapter = Mage::getModel($batchModel->getAdapter());
            $adapter->setBatchParams($batchModel->getParams());

            $errors = array();
            $saved = 0;
            $relatedData = array();
            $cache = Mage::getSingleton('core/cache');

            foreach ($rowIds as $importId) {
                $batchImportModel->load($importId);
                if (!$batchImportModel->getId()) {
                    $errors[] = Mage::helper('dataflow')->__('Skip undefined row.');
                    continue;
                }

                try {
                    $importData = $batchImportModel->getBatchData();
                    $adapter->saveRow($importData);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    continue;
                }
                $relatedData = $cache->load('relatedData') ? unserialize($cache->load('relatedData')) : array();
                if ($importData['Configurable'])
                    $relatedData[$importData['Style NO.']] = $importData['Wear This With'];
                else
                    $relatedData[$importData['SKU']] = $importData['Wear This With'];
                $cache->save(serialize($relatedData), 'relatedData', array('relatedData'), 60 * 60 * 20);
                $saved++;
            }

            if (method_exists($adapter, 'getEventPrefix')) {
                /**
                 * Event for process rules relations after products import
                 */
                Mage::dispatchEvent($adapter->getEventPrefix() . '_finish_before', array(
                    'adapter' => $adapter
                ));

                /**
                 * Clear affected ids for adapter possible reuse
                 */
                $adapter->clearAffectedEntityIds();
            }

            $result = array(
                'savedRows' => $saved,
                'errors' => $errors
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function getIoAdapter()
    {
        if ($this->_ioAdapter === NULL) {
            $this->_ioAdapter = new Varien_Io_File();
            $this->_ioAdapter->setAllowCreateFolders(TRUE);
        }
        return $this->_ioAdapter;
    }

    /**
     * @param string $sku
     * @return Gri_CatalogCustom_Model_Product
     */
    public function getProductBySku($sku)
    {
        return ($id = Mage::getSingleton('catalog/product')->getIdBySku($sku)) ?
            Mage::getModel('catalog/product')->load($id) : FALSE;
    }

    public function importImageAction()
    {
        $totalImage = $this->saveImage();
        Mage::register('totalImage', $totalImage);
        $failedImageAmount = 0;
        $imgExtensions = array('jpg', 'jpeg', 'gif', 'png');
        $productMedia = Mage::getBaseDir('media') . DS . 'import';
        $this->_imageFiles = array();
        $this->readImageFiles();
        foreach ($this->_imageFiles as $v) {
            $fileData = pathinfo($productMedia . DS . $v);
            if (!in_array(strtolower($fileData['extension']), $imgExtensions))
                continue;
            $failedImageAmount++;
        }
        Mage::register('failedImage', $failedImageAmount);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function readImageFiles($directory = NULL)
    {
        $imgExtensions = array('jpg', 'jpeg', 'gif', 'png');
        $base = Mage::getBaseDir('media') . DS . 'import';
        if ($directory === NULL) {
            $directory = $base;
            $imageSource = Mage::getBaseDir('var') . DS . 'import' . DS . 'productImages.zip';
            Mage::helper('gri_importdata')->unzipImages($imageSource, $base);
        }
        $dirHandler = dir($directory);
        while (FALSE !== ($entry = $dirHandler->read())) {
            if ($entry{0} == '.')
                continue;
            if (is_dir($directory . DS . $entry)) {
                $this->readImageFiles($directory . DS . $entry);
                continue;
            }
            $fullDir = dirname($realPath = $directory . DS . $entry);

            $fileData = pathinfo($realPath);
            if (!in_array(strtolower($fileData['extension']), $imgExtensions))
                continue;
            if ($fullDir == $base) {
                $this->_imageFiles[] = $entry;
            } else {
                $this->_imageFiles[] = substr($fullDir, strlen($base) + 1) . DS . $entry;
            }
        }
        return $this->_imageFiles;
    }

    public function removeFile()
    {
        $source = Mage::getBaseDir('var') . DS . 'import' . DS . 'import_products.xml';
        $destination = Mage::getBaseDir('var') . DS . 'import' . DS . 'history' . DS . 'import_products.' .
            Mage::app()->getLocale()->date()->toString('YYYYMMddHHmmss') . '.xml';
        if (is_file($source)) {
            file_exists($dir = dirname($destination)) or mkdir($dir, 0777, TRUE);
            is_file($destination) and unlink($destination);
            rename($source, $destination);
        }
        return;
    }

    public function saveImage()
    {
        $imgExtensions = array('jpg', 'jpeg', 'gif', 'png');
        $productMedia = Mage::getBaseDir('media') . DS . 'import';
        $this->readImageFiles();
        $imageData = array();
        $swatchData = array();
        $totalMedia = 0;
        $ioAdapter = $this->getIoAdapter();

        foreach ($this->_imageFiles as $v) {
            $fileData = pathinfo($productMedia . DS . $v);
            if (!in_array(strtolower($fileData['extension']), $imgExtensions))
                continue;

            // $fileNameData = explode('_', strtolower($fileData['filename']));
            $fileNameData = Mage::helper('gri_importdata')->getFileNameData(strtolower($fileData['filename']));
            if (count($fileNameData) != 3) continue;

            $totalMedia++;
            if (in_array('swatch', $fileNameData)) {
                if (!isset($swatchData[$fileNameData[0]]))
                    $swatchData[$fileNameData[0]] = array();
                $swatchData[$fileNameData[0]][$fileNameData[1]] = $v;
            } else {
                if (!isset($imageData[$fileNameData[0]]))
                    $imageData[$fileNameData[0]] = array();
                $imageData[$fileNameData[0]][$fileNameData[1]][$fileNameData[2]] = $v;
            }
        }

        foreach ($imageData as $sku => $v) {
            // sku of product
            if (!$product = $this->getProductBySku($sku)) continue;
            $existingImages = array();
            foreach($product->getMediaGalleryImages() as $image) {
                $image->getFile() == 'no_selection' or $existingImages[basename($image->getFile())] = $image->getFile();
            }
            $product->setExistingImages($existingImages);
            $first = $existingImages ? $product->getImage() : FALSE;
            foreach ($v as $color => $colorImages) {
                ksort($colorImages);
                foreach ($colorImages as $pos => $file) {
                    $fileName = Mage_Core_Model_File_Uploader::getCorrectFileName(basename($file));
                    $destinationPath = Mage_Core_Model_File_Uploader::getDispretionPath($fileName);
                    $destinationFile = $product->getMediaConfig()->getMediaPath($destinationPath . DS . $fileName);
                    $ioAdapter->open(array(
                        'path' => dirname($destinationFile),
                    ));
                    is_file($destinationFile) and $ioAdapter->rm($destinationFile);
                    if (isset($existingImages[basename($file)])) {
                        unset($colorImages[$pos]);
                        $ioAdapter->mv($productMedia . DS . $file, $destinationFile);
                    }
                }
                $first or $first = array_shift($colorImages);
                $v[$color] = implode(',', $colorImages);
            }

            $gallery = array(
                'image' => $first,
                'small_image' => $first,
                'thumbnail' => $first,
                'gallery' => implode(',', $v),
            );

            // Save gallery images and swatch image
            $this->appendImage($product, $gallery);
            $this->saveSwatchImage($product, $swatchData);
            // Remove processed swatch image
            unset($swatchData[strtolower($product->getSku())]);
            $product->clearInstance();
        }
        // Save unprocessed swatch images
        foreach ($swatchData as $sku => $v) {
            if (!$product = $this->getProductBySku($sku)) continue;
            $this->saveSwatchImage($product, $swatchData);
            $product->clearInstance();
        }
        return $totalMedia;
    }


    public function saveRelation()
    {
        $cache = Mage::getSingleton('core/cache');
        if ($relatedData = unserialize($cache->load('relatedData'))) {
            foreach ($relatedData as $k => $v) {
                $v = explode(',', $v);
                if (!$product = $this->getProductBySku($k)) continue;
                $relatedProducts = array();
                foreach ($v as $sub_v) {
                    if ($id = Mage::getSingleton('catalog/product')->getIdBySku($sub_v)) {
                        $relatedProducts[$id] = array('position' => 0);
                    }
                }
                if ($relatedProducts)
                    $product->setRelatedLinkData($relatedProducts)->save();
                $product->clearInstance();
            }

            $cache->clean(array('relatedData'));
        }
    }

    public function saveSwatchImage(Mage_Catalog_Model_Product $product, array $data)
    {
        if (isset($data[strtolower($product->getSku())])) {
            /* @var $swatchHelper Magiatec_Colorswatch_Helper_Swatch */
            $swatchHelper = Mage::helper('magiatecolorswatch/swatch');
            /* @var $swatch Magiatec_Colorswatch_Model_Product */
            $swatch = Mage::getModel('magiatecolorswatch/product');
            $ioAdapter = $this->getIoAdapter();
            $v = $data[strtolower($product->getSku())];

            foreach ($product->getTypeInstance()->getConfigurableAttributes($product) as $attribute) {

                if (!$swatchHelper->checkAttributes($attribute->getAttributeId()))
                    continue;

                if ($prices = $attribute->getPrices()) foreach ($prices as $key => $value) {
                    $swatchHelper->setCurrentProduct($product);
                    $swatchOptions = $swatchHelper->getSwatchDropDown()->getOptions();
                    foreach ($swatchOptions as $option) {
                        if ($option['value'] == $value['value_index']) {
                            $label = $option['label'];
                            break;
                        }
                    }
                    $image = FALSE;

                    foreach ($v as $sub_v) {

                        if (in_array($label, explode('_', $sub_v))) {
                            $image = $sub_v;

                            break;
                        }
                    }
                    if ($image) {
                        $swatch->unsetData();
                        if ($model = $swatch->loadByAttributes(array(
                            'product_super_attribute_id' => $value['product_super_attribute_id'],
                            'value_index' => $value['value_index'],
                        ))) {
                            $swatch = $model;
                        }
                        $swatch->setData('product_super_attribute_id', $value['product_super_attribute_id'])
                            ->setData('value_index', $value['value_index'])
                            ->setData('image', $baseName = basename($image))
                            ->save();

                        // move image
                        $destination = Mage::helper('magiatecolorswatch')->getSwatchesFile($baseName);
                        $ioAdapter->open(array(
                            'path' => dirname($destination)
                        ));
                        $file = realpath(Mage::getBaseDir('media') . DS . 'import' . DS . $image);
                        is_file($destination) and $ioAdapter->rm($destination);
                        $ioAdapter->mv($file, $destination);
                    }
                }
            }
            $swatch->unsetData();
            unset($swatch);
        }
    }

}
