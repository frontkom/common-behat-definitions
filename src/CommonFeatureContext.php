<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\MinkExtension\Context\RawMinkContext;

class CommonFeatureContext extends RawMinkContext
{
    protected const WIDTH = 1440;
    protected const HEIGHT = 1000;

    use JsErrorsDumper;
    use ElementExistsTrait;

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
            throw new \Exception(
                'Expected to find exactly one element by selector ' .
                $selector .
                ' but found ' . count($elements) . ' elements'
            );
        }
        /** @var \Behat\Mink\Element\NodeElement $element */
        $element = reset($elements);
        $i = 0;
        while (true) {
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

    /**
     * Set the viewport to something defined as desktop.
     *
     * If you need to override this, simply create a class that extends this
     * class, and reference that class inside your behat.yml file.
     *
     * @Given viewport is desktop
     * @Given the viewport is desktop
     */
    public function iSetDesktopViewport()
    {
        $mink = $this->getMink();
        if (!$mink->getSession()->isStarted()) {
            $mink->getSession()->start();
        }
        $this->getSession()->resizeWindow(self::WIDTH, self::HEIGHT, 'current');
    }

    /**
     * Scroll something into view.
     *
     * @Then I scroll element :selector into view
     * @Then I scroll :selector into view
     */
    public function scrollSelectorIntoView($selector)
    {
        $function = <<<JS
  (function(){
    var elem = jQuery("$selector")[0];
    elem.scrollIntoView(false);
    window.scrollBy(0, 100)
  })()
  JS;
        try {
            $this->getSession()->executeScript($function);
        } catch (\Exception $e) {
            throw new \Exception("ScrollIntoView failed: " . $e->getMessage());
        }
    }
}
