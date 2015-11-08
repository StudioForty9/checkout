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
        if (!$this->getRequest()->isPost()) {
            Mage::getSingleton('core/session')->addError('That action not allowed.');
            return $this->_redirect('/');
        }

        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId(); 
        $order = Mage::getModel('sales/order')->load($orderId);
        $store = Mage::app()->getStore();
        $customerBuilder = new Studioforty9_Checkout_Model_Customer_Builder($order, $store);

        try {
            $customer = $customerBuilder->build(
                $order->getCustomerEmail(),
                $this->getRequest()->getPost('password')
            );
            $order->setCustomerId($customer->getId());
            $order->save();
            // Customer is created and associated to the last order, send a welcome email.
            $customer->sendNewAccountEmail('registered', '', $store->getId());
        }
        catch (Exception $e) {
            Mage::logException($e);
            // TODO: Pull the error message out into configuration
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return $this->_redirect('/');
        }

        // TODO: Pull the success message out into configuration
           Mage::getSingleton('core/session')->addSuccess(
            'Thank you for signing up, you can now log in with the password you chose.'
        );
        
        return $this->_redirect('/');
    }
    
    /**
     * Find a customer by email address.
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
