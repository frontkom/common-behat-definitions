<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\MinkExtension\Context\RawMinkContext;

class CommonFeatureContext extends RawMinkContext
{
    use JsErrorsDumper;

    /**
     * Wait until an element is visible.
     *
     * @Given /^I wait until element "([^"]*)" is visible/
     * @Given /^I wait until element '([^']*)' is visible$/
     */
    public function iWaitUntilElementIsVisible($selector)
    {
        $elements = $this->getSession()->getPage()->findAll('css', $selector);
        if (count($elements) !== 1) {
          throw new \Exception('Expected to find exactly one element by selector ' . $selector . ' but found ' . count($elements) . ' elements');
        }
        /** @var \Behat\Mink\Element\NodeElement $element */
        $element = reset($elements);
        $i = 0;
        while (TRUE) {
            if ($element->isVisible()) {
              return;
            }
            sleep(1);
            $i++;
            if ($i > 10) {
                throw new \Exception('Element never became visible');
            }
        }
    }

    /**
     * Step definition to find links by URL.
     *
     * @Then /^I should see a link to "([^"]*)"$/
     */
    public function iShouldSeeALinkTo($url)
    {
        $this->assertSession()->elementExists('css', 'a[href="' . $url . '"]');
    }

    /**
     * Step definition to wait a little.
     *
     * @Then (I )wait :count second(s)
     */
    public function iWaitSeconds($count)
    {
        usleep($count * 1000000);
    }

}
