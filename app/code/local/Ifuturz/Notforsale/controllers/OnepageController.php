<?php
/**
 * @package Ifuturz_Notforsale
*/
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Ifuturz_Notforsale_OnepageController extends Mage_Checkout_OnepageController
{
    public function saveOrderAction()
    {       
        if ($this->_expireAjax()) {
            return;
        }
		/* start code by ifuturz */
		$result = array();	
		$_blockData = $this->getLayout()->createBlock('notforsale/notforsale');
		$regionId = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getRegionId();
		
		if(in_array($regionId,$_blockData->checkShiprestriction()))
		{			
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$statename = $connection->fetchRow("select * from directory_country_region WHERE region_id=".$regionId." AND country_id = 'US' limit 1");
			//Mage::getSingleton('core/session')->setCheckouterror('You have an product in your shopping cart that can not be shipped to the '.$statename["default_name"].' due to '.$statename["code"].' state regulation. Please remove the items from your card.');					
			
			$result['error'] = true;
			$result['error_messages'] = $this->__('You have an product in your shopping cart that can not be shipped to the '.$statename["default_name"].' due to '.$statename["code"].' state regulation. Please remove the items from your cart.');			
			
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			
			return;
					
			//$this->_redirect(Mage::getUrl('checkout/cart'));							
			//return;			
		}
		//die('dfsf');
		/* end code by ifuturz */
        $result = array();
        try {
            $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                $diff = array_diff($requiredAgreements, $postedAgreements);
                if ($diff) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }

            $data = $this->getRequest()->getPost('payment', array());
            if ($data) {
                $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                    | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                    | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }

            $this->getOnepage()->saveOrder();

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();			
            $result['success'] = true;
            $result['error']   = false;
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            $gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();
            if ($gotoSection) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }
            $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();
            if ($updateSection) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

}
