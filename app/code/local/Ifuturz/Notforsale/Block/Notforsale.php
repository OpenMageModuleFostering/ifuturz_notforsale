<?php
/**
 * @package Ifuturz_Notforsale
*/
class Ifuturz_Notforsale_Block_Notforsale extends Mage_Core_Block_Template
{  	
	public function checkShiprestriction()
	{
		$session= Mage::getSingleton('checkout/session');
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		foreach($session->getQuote()->getAllItems() as $item)
		{
			$productType = $item->getProduct()->getTypeId();		  	
			if($productType == 'configurable' || $productType == 'bundle')
			{				
				continue;							
			}			
			$productid = $item->getProductId();	
			$productload = Mage::getModel('catalog/product')->load($productid);						
			$optionvalues = $productload->getAttributeText('not_for_sale_to');	
			if(count($optionvalues) > 1)
			{
				if($productload->getNot_for_sale_to()!='')
				{ 							
					for($i=0;$i<count($optionvalues);$i++)
					{
						$statedata = $connection->query("select * from directory_country_region WHERE code='".$optionvalues[$i]."'");	
						$statename = $statedata->fetch();					
						$regionids[] = $statename['region_id'];	
					}
				}	
			}
			else if($optionvalues != '')
			{
					$statedata = $connection->query("select * from directory_country_region WHERE code='".$optionvalues."'");	
					$statename = $statedata->fetch();					
					$regionids[] = $statename['region_id'];
			}
		}
		return $regionids;
	}	
}
