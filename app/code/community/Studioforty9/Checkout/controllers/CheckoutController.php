<?php
/**
 * StudioForty9 Checkout
 *
 * @category  Studioforty9
 * @package   Studioforty9_Checkout
 * @author    StudioForty9 <info@studioforty9.com>
 * @copyright 2015 StudioForty9 (http://www.studioforty9.com)
 * @license   https://github.com/studioforty9/checkout/blob/master/LICENCE BSD
 * @version   1.0.0
 * @link      https://github.com/studioforty9/checkout
 */

/**
 * Studioforty9_Checkout_CheckoutController
 *
 * @category   Studioforty9
 * @package    Studioforty9_Checkout
 * @subpackage Controller
 */
class Studioforty9_Checkout_CheckoutController extends Mage_Core_Controller_Front_Action
{
    /**
     * Register a guest.
     *
     * @return Mage_Core_Controller_Response_Http
     */
	public function registerGuestAction()
	{
		if ($this->getRequest()->isPost()) {
            return;
        }
        
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();

		$orderId = Mage::getSingleton('checkout/session')->getLastOrderId(); 
		$order = Mage::getModel('sales/order')->load($orderId);
		
		$shippingId = $shippingId = $order->getShippingAddress()->getId();
		$shippingAddress = Mage::getModel('sales/order_address')->load($shippingId);
		
		$billingId = $shippingId = $order->getBillingAddress()->getId();
		$billingAddress = Mage::getModel('sales/order_address')->load($billingId);

		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId($websiteId)
        	->setStore($store)
            ->setFirstname($billingAddress->getFirstname())
            ->setLastname($billingAddress->getLastname())
            ->setEmail($order->getCustomerEmail())
            ->setPassword($this->getRequest()->getPost('password'));

        try {
		    $customer->save();
		}
		catch (Exception $e) {
		    Mage::getSingleton('core/session')->addError($e->getMessage());
       		return Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
		}

       	$customerId = $customer->getId();
       	$customerBillingAddress = Mage::getModel("customer/address");
		$customerBillingAddress->setCustomerId($customerId)
	        ->setFirstname($billingAddress->getFirstname())
	        ->setLastname($billingAddress->getLastname())
	        ->setCountryId($billingAddress->getCountryId())
			->setRegionId($billingAddress->getRegionId()) 
			->setRegion($billingAddress->getRegion()) 
	        ->setPostcode($billingAddress->getPostCode())
	        ->setCity($billingAddress->getCity())
	        ->setTelephone($billingAddress->getTelephone())
	        ->setFax($billingAddress->getFax())
	        ->setCompany($billingAddress->getCompany())
	        ->setStreet($billingAddress->getStreet())
	        ->setIsDefaultBilling('1')
	        ->setSaveInAddressBook('1');
		
		try {
		    $customerBillingAddress->save();
		}
		catch (Exception $e) {
		    Mage::getSingleton('core/session')->addError($e->getMessage());
       		return Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
		}

       	$customerShippingAddress = Mage::getModel("customer/address");
		$customerShippingAddress->setCustomerId($customerId)
	        ->setFirstname($shippingAddress->getFirstname())
	        ->setLastname($shippingAddress->getLastname())
	        ->setCountryId($shippingAddress->getCountryId())
			->setRegionId($shippingAddress->getRegionId()) 
			->setRegion($shippingAddress->getRegion()) 
	        ->setPostcode($shippingAddress->getPostCode())
	        ->setCity($shippingAddress->getCity())
	        ->setTelephone($shippingAddress->getTelephone())
	        ->setFax($shippingAddress->getFax())
	        ->setCompany($shippingAddress->getCompany())
	        ->setStreet($shippingAddress->getStreet())
	        ->setIsDefaultShipping('1')
	        ->setSaveInAddressBook('1');
		
		try {
		    $customerShippingAddress->save();
		}
		catch (Exception $e) {
		    Mage::getSingleton('core/session')->addError($e->getMessage());
       		return Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
		}

       	$order->setCustomerId($customerId);

       	try {
		   $order->save();
		}
		catch (Exception $e) {
		    Mage::getSingleton('core/session')->addError($e->getMessage());
       		return Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
		}

       	Mage::getSingleton('core/session')->addSuccess('Thank you for signing up, you can now log in with the password you chose.');
        $customer->sendNewAccountEmail('registered', '', $store->getId());
       	return Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
	}
    
    /**
     * Find a user.
     *
     * @return Mage_Core_Controller_Response_Http
     */
	public function userAction()
    {
		if (!$this->getRequest()->isPost()) {
			return $this->getResponse()->setBody(0);
		}
        
		try {
			$email = $this->getRequest()->getPost('email');
			$customer = Mage::getModel('customer/customer');
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
			$customer->loadByEmail($email);
            
			return $this->getResponse()->setBody((int) $customer->getId());
		} catch (Exception $e) {
			return $this->getResponse()->setBody($e->getMessage());
		}
	}
}
