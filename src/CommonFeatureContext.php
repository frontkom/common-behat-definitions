<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\MinkExtension\Context\RawMinkContext;

class CommonFeatureContext extends RawMinkContext
{
    protected const WIDTH = 1440;
    protected const HEIGHT = 1000;

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
    public function scrollSelectorIntoView($selector) {
      $function = <<<JS
  (function(){
    var elem = jQuery("$selector")[0];
    elem.scrollIntoView(false);
    window.scrollBy(0, 100)
  })()
  JS;
        try {
            $this->getSession()->executeScript($function);
        }
        catch (\Exception $e) {
            throw new \Exception("ScrollIntoView failed: " . $e->getMessage());
        }
    }

    /**
     * @Then I( should) see the :tag element with the :attribute attribute set to :value
     */
    public function assertRegionElementAttribute($tag, $attribute, $value)
    {
        $elements = $this->getSession()->getPage()->findAll('css', $tag);
        if (empty($elements)) {
            throw new \Exception(sprintf('The element "%s" was not found on the page %s', $tag, $this->getSession()->getCurrentUrl()));
        }
        if (!empty($attribute)) {
            $found = FALSE;
            $attrfound = FALSE;
            foreach ($elements as $element) {
                $attr = $element->getAttribute($attribute);
                if (NULL !== $attr) {
                    $attrfound = TRUE;
                    if (strpos($attr, "$value") !== FALSE) {
                        $found = TRUE;
                        break;
                    }
                }
            }
            if (!$found) {
                if (!$attrfound) {
                    throw new \Exception(sprintf('The "%s" attribute is not present on the element "%s" on the page %s', $attribute, $tag, $this->getSession()->getCurrentUrl()));
                } else {
                    throw new \Exception(sprintf('The "%s" attribute does not equal "%s" on the element "%s" on the page %s', $attribute, $value, $tag, $this->getSession()->getCurrentUrl()));
                }
            }
        }
    }

    /**
     * @Then /^I hover mouse over the "(?P<selector>[^"]*)" element$/
     */
    public function iHoverMouseOver($selector)
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('css', $selector);
        if ($element === NULL) {
            throw new \Exception("Element '$selector' NOT found");
        }

        $element->mouseOver();
    }

    /**
     * The dirty but only way to test clicking without being redirected.
     *
     * @Given Link redirections are disabled
     */
    public function disableLinkRedirections()
    {
        $session = $this->minkContext->getSession();
        $script = "jQuery('a').click(function (e) {e.preventDefault()});";
        $session->evaluateScript($script);
    }

}
