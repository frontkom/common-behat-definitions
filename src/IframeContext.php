<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\MinkExtension\Context\RawMinkContext;

class IframeContext extends RawMinkContext
{
    /**
     * @Given I switch to iframe with selector :selector
     */
    public function swithToIframeWithSelector($selector)
    {
        $element = $this->getSession()->getPage()->find('css', $selector);
        if (!$element) {
            throw new \Exception('Element not found');
        }
        $name_attribute = $element->getAttribute('name');
        if (empty($name_attribute)) {
            throw new \Exception('Element does not have a name attribute');
        }
        $this->getSession()->switchToIFrame($name_attribute);
    }
}
