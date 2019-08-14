<?php
/**
 * @package Ifuturz_Notforsale
*/
class Ifuturz_Notforsale_Helper_Product_Configuration extends Mage_Catalog_Helper_Product_Configuration
{
	 /**
     * Retrieves configuration options for configurable product
     *
     * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getConfigurableOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $product = $item->getProduct();
        $typeId = $product->getTypeId();
        if ($typeId != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
             Mage::throwException($this->__('Wrong product type to extract configurable options.'));
        }		
        $attributes = $product->getTypeInstance(true)
            ->getSelectedAttributesInfo($product);					
		
		$attributes[0]['pid'] = $item->getOptionByCode('simple_product')->getProduct()->getId(); /* code added by ifuturz */

        return array_merge($attributes, $this->getCustomOptions($item));
    }
}
