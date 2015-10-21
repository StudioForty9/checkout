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
 * Studioforty9_Checkout_Block_Onepage_Login
 *
 * @category   Studioforty9
 * @package    Studioforty9_Checkout
 * @subpackage Block
 */
class Studioforty9_Checkout_Block_Onepage_Login extends Mage_Checkout_Block_Onepage_Login
{
    /**
     * Set the Login progress block step data
     */
    protected function _construct()
    {
        if (!$this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('login', array(
                'label' => Mage::helper('checkout')->__('Email Address'),
                'allow' => true
            ));
        }

        Mage_Checkout_Block_Onepage_Abstract::_construct();
    }
}
