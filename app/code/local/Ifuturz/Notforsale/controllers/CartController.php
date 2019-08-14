<?php
/**
 * @package Ifuturz_Notforsale
*/
require_once 'Mage/Checkout/controllers/CartController.php';
class Ifuturz_Notforsale_CartController extends Mage_Checkout_CartController
{
    public function estimatePostAction()
    {
		
        $country    = (string) $this->getRequest()->getParam('country_id');
        $postcode   = (string) $this->getRequest()->getParam('estimate_postcode');
        $city       = (string) $this->getRequest()->getParam('estimate_city');
        $regionId   = (string) $this->getRequest()->getParam('region_id');
        $region     = (string) $this->getRequest()->getParam('region');
		
		/*start code by ifuturz*/
		$regionModel = Mage::getModel('directory/region')->load($regionId);
		$region_code = $regionModel->getCode();		
		$_blockData = $this->getLayout()->createBlock('notforsale/notforsale');
		
		if(in_array($regionId,$_blockData->checkShiprestriction()))
			{				
				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
				$statename = $connection->fetchRow("select * from directory_country_region WHERE region_id=".$regionId." AND country_id = 'US' limit 1");
				
				if(count($statename) > 0)
				{
					 $this->_getSession()->addError($this->__('You have an product in your shopping cart that can not be shipped to the '.$statename["default_name"].' due to '.$statename["code"].' state regulation. Please remove the items from your cart.'));
					 $this->_goBack();
					 return;
				}		
			}
		/*end code by ifuturz*/	
        $this->_getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
        $this->_getQuote()->save();
        $this->_goBack();
    }

}
