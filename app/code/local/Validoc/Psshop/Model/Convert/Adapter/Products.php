<?php

class Validoc_Psshop_Model_Convert_Adapter_Products extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    const XML_PATH_EXPORT_LOCAL_VALID_PATH = 'general/file/importexport_local_valid_paths';

    /**
     * Load data
     *
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function load()
    {
        //
    }

    /**
     * Save result to destination file from temporary
     *
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function save()
    {
        //
    }

    public function saveRow(array $importData) {
        /* Implement your custom processing in here */
        $requiredFields = array(
            'ItemId',
            'ItemName',
            'CostUnitAmountMST'
        );
        //Make sure required fields exist
        if (!$this->checkFieldsExist($requiredFields, $importData)) {
            return false;
        }

        // $product = Mage::getModel('catalog/product');
        $product = new Mage_Catalog_Model_Product();
        // Build the product
        $product->setSku($importData['ItemId']);
        $product->setTypeId('simple');
        $product->setName($importData['ItemName']);
        $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        $product->setDescription('Full description here');
        $product->setShortDescription('Short description here');
        $product->setPrice($importData['CostUnitAmountMST']); # Set some price

        // Default Magento attribute
        $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        $product->setStatus(1);
        $product->setWeight(1.0);
        $entityTypeCollection = Mage::getModel('eav/entity_type')->getCollection();
        $entityTypeCollection->addFieldToFilter('entity_type_code', 'catalog_product');
        $attributeSetId = $entityTypeCollection->getFirstItem()->getId();
        $product->setAttributeSetId($attributeSetId);
        $product->setTaxClassId(0); # My default tax class
        $product->setStockData(array(
            'is_in_stock' => 1,
            'qty' => 99999
        ));
        $product->setCreatedAt(strtotime('now'));
        try {
            $product->save();
            return true;
        } catch(Exception $e) {
            Mage::log($e->getMessage(), Zend_Log::ERR, 'sync_products_psshop.log');
        }

    }

    protected function checkFieldsExist(array $fields, array $importData) {
        $rowValid = true;
        foreach($fields as $field) {
            if (empty($importData[$field])) {
                $message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', $field);
                Mage::throwException($message);
            }
        }
        return $rowValid;
    }
}