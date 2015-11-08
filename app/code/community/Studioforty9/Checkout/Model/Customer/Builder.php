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
 * Studioforty9_Checkout_Model_Customer_Builder
 *
 * @category   Studioforty9
 * @package    Studioforty9_Checkout
 * @subpackage Model
 */
class Studioforty9_Checkout_Model_Customer_Builder
{
    /** @var Mage_Sales_Model_Order */
    protected $_order;

    /** @var Mage_Core_Model_Store */
    protected $_store;

    /** @var Mage_Core_Model_Website */
    protected $_website;

    /** @var Mage_Customer_Model_Address */
    protected $_orderBillingAddress;

    /** @var Mage_Customer_Model_Address */
    protected $_orderShippingAddress;
    
    /**
     * Create a new customer builder instance.
     *
     * @param Mage_Sales_Model_Order  $order
     * @param Mage_Core_Model_Store   $store
     * @param Mage_Core_Model_Website $website
     */
    public function __construct(
        Mage_Sales_Model_Order $order,
        Mage_Core_Model_Store $store = null,
        Mage_Core_Model_Website $website = null
    ) {
        $this->setOrder($order);
        $this->setStore($store);
        $this->setWebsite($website);
    }
    
    /**
     * Set an instance of the order object to derive customer information.
     *
     * @param  Mage_Sales_Model_Order $order
     * @return self
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }
    
    /**
     * Get an instance of the order object to derive customer information.
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }
    
    /**
     * Set an instance of the store object to which to relate the customer.
     *
     * @param  Mage_Core_Model_Store $order
     * @return self
     */
    public function setStore(Mage_Core_Model_Store $store = null)
    {
        if (!$store) {
            $this->_store = Mage::app()->getStore();
        }
        
        $this->_store = $store;
        
        return $this;
    }
    
    /**
     * Get an instance of the store object to which to relate the customer.
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_store;
    }
    
    /**
     * Set an instance of the website object to which to relate the customer.
     *
     * @param  Mage_Core_Model_Website $order
     * @return self
     */
    public function setWebsite(Mage_Core_Model_Website $website = null)
    {
        $this->_website = is_null($website) ? Mage::app()->getWebsite() : $website;
        
        return $this;
    }
    
    /**
     * Get an instance of the website object to which to relate the customer.
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        return $this->_website;
    }
    
    /**
     * Get the id of the current website.
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if (!$this->_website) {
            $this->setWebsite(null);
        }
        
        return (int) $this->getWebsite()->getId();
    }
    
    /**
     * Build a customer from the data in the last order.
     *
     * @param  string $email
     * @param  string $password
     * @return Mage_Customer_Model_Customer
     */
    public function build($email, $password)
    {
        $customer = $this->_createCustomer($email, $password);
        $customer->save();
       	$customerId = $customer->getId();
        $this->_createCustomerBillingAddress($customerId)->save();
        $this->_getCustomerShippingAddress($customerId)->save();
        
        return $customer;
    }
    
    /**
     * Create a customer with the provided email and password.
     *
     * @param  string $email
     * @param  string $password
     * @return Mage_Customer_Model_Customer
     */
    private function _createCustomer($email, $password)
    {
		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId($this->getWebsiteId())
        	->setStore($this->getStore())
            ->setFirstname($this->_getOrderBillingAddress()->getFirstname())
            ->setLastname($this->_getOrderBillingAddress()->getLastname())
            ->setEmail($email)
            ->setPassword($password);
        
        return $customer;
    }
    
    /**
     * Create a new billing adress object for the customer.
     *
     * @param  int $customerId
     * @return Mage_Customer_Model_Address
     */
    private function _createCustomerBillingAddress($customerId)
    {
        $billingAddress = $this->_getOrderBillingAddress();
        $address = Mage::getModel("customer/address");
		$address->setCustomerId($customerId)
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
        
        return $address;
    }
    
    /**
     * Create a new shipping adress object for the customer.
     *
     * @param  int $customerId
     * @return Mage_Customer_Model_Address
     */
    private function _getCustomerShippingAddress($customerId)
    {
        $shippingAddress = $this->_getOrderShippingAddress();
        $address = Mage::getModel("customer/address");
		$address->setCustomerId($customerId)
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
        
        return $address;
    }
    
    /**
     * Get the order address instance for the supplied billing address.
     *
     * @param  int $customerId
     * @return Mage_Sales_Model_Order_Address
     */
    private function _getOrderBillingAddress()
    {
        if (! $this->_orderBillingAddress) {
    		$this->_orderBillingAddress = Mage::getModel('sales/order_address')
                 ->load($this->getOrder()->getBillingAddress()->getId());
        }
        
        return $this->_orderBillingAddress;
    }
    
    /**
     * Get the order address instance for the supplied shipping address.
     *
     * @return Mage_Sales_Model_Order_Address
     */
    private function _getOrderShippingAddress()
    {
        if (! $this->_orderShippingAddress) {
    		$this->_orderShippingAddress = Mage::getModel('sales/order_address')
                 ->load($this->getOrder()->getBillingAddress()->getId());
        }
        
        return $this->_orderShippingAddress;
    }
}
