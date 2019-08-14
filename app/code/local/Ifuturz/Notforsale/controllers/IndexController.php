<?php
/**
 * @package Ifuturz_Notforsale
*/
class Ifuturz_PriceProtection_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Price Protection"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("price protection", array(
                "label" => $this->__("Price Protection"),
                "title" => $this->__("Price Protection")
		   ));

      $this->renderLayout(); 
	  
    }
	
	public function saveAction() {
		

		 	 $data = $this->getRequest()->getPost();
		
 			 $model = Mage::getModel('priceprotection/priceprotection');
			
			 $model->setName($data['name']); 
			
			 $model->setEmail($data['email']);		 
			 $model->setAddressline1($data['addressline1']);
			 
			 $model->setAddressline2($data['addressline2']);
			
			 $model->setCity($data['city']);
			
			 $model->setState($data['state']);
			
			 $model->setZipcode($data['zipcode']);
		
			 $model->setOrderNumber($data['order_number']);
			 $model->setItemNumber($data['item_number']);
			 $model->setItemDescription($data['item_description']);
			 $model->setOriginalPurchaseDate($data['original_purchase_date']);
			 $model->setOriginalPurchasePrice($data['original_purchase_price']);
			 $model->setPurchasePrice($data['purchase_price']);
			 $model->setPurchaseZipcode($data['purchase_zipcode']);
			 
			 $model->setSame($data['same']); 
			
			 $model->setAvailable($data['available']);
			 		 
			 $model->setOriginal($data['original']);
		
			 $model->setCreatedTime(now());

			 $model->setUpdateTime(now());

			 $model->save();
			 
			// echo "<pre>";print_r($model->getData());die;
			
			 Mage::getSingleton('core/session')->addSuccess(Mage::helper('priceprotection')->__('Your form has been send successfully.'));

			 $this->_redirect('priceprotection');	 
	 
	}
}