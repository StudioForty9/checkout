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

namespace Custom;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Custom context.
 */
class SimpleCheckoutContext extends RawMinkContext implements SnippetAcceptingContext
{

    use \AbstractContext;

    /**
     * @Given /^the Simple Checkout is Enabled$/
     */
    public function theSimpleCheckoutIsEnabled()
    {
        $enabled = \Mage::getStoreConfig('checkout/studioforty9_checkout/active');
        \PHPUnit_Framework_Assert::assertTrue((bool)$enabled);
    }

    /**
     * @When I enter an non-registered email address
     */
    public function iEnterAnNonRegisteredEmailAddress()
    {
        $email = 'non-registered-' . $this->_getEmailAddress();
        $this->_enterEmailAddress($email);
    }

    /**
     * @When I enter an registered email address
     */
    public function iEnterAnRegisteredEmailAddress()
    {
        $this->_enterEmailAddress($this->_getEmailAddress());
    }

    protected function _getEmailAddress()
    {
        $date = date('Ymd');
        return "behat-$date@sf9.ie";
    }

    protected function _enterEmailAddress($email)
    {
        $id = 'check-email';
        $input = $this->getSession()->getPage()->find('xpath', '//*[@id="' . $id .'"]');
        \PHPUnit_Framework_Assert::assertNotNull($input);
        $this->getSession()->getPage()->fillField($id, $email);
        $this->getSession()->getDriver()->click('//button[@onclick="checkEmail(this);"]');
        sleep(2);
    }
}
