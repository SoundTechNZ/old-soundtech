<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_PaymentDetect
 */


/**
 * Class Image
 *
 * @author Artem Brunevski
 */
class Amasty_PaymentDetect_Model_System_Config_Backend_Image extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
    /**
     * Save uploaded file before saving config value
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_File
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();

        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']) {

            $uploadDir = $this->_getUploadDir();

            try {
                $file = array();
                $tmpName = $_FILES['groups']['tmp_name'];
                $file['tmp_name'] = $tmpName[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $name = $_FILES['groups']['name'];
                $file['name'] = $name[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $uploader = new Mage_Core_Model_File_Uploader($file);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $result = $uploader->save($uploadDir);

            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                // Delete record before it is saved
                $this->delete();
                // Prevent record from being saved, since it was just deleted
                $this->_dataSaveAllowed = false;
            } else {
                if (isset($value['value'])) {
                    $this->setValue($value['value']);
                }
            }
        }

        return $this;
    }
}