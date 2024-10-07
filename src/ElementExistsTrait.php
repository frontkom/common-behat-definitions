<?php

namespace Frontkom\CommonBehatDefinitions;

trait ElementExistsTrait
{
    /**
     * Wait until element exists.
     *
     * Use this to wait for iframes or slow ajax. This is different than waiting
     * for an element to be visible, as that assumes the element is already on the
     * page, while this can wait until an element actually appears.
     *
     * @Given I wait until element :arg1 exists
     * @Given I wait until element :arg1 exists max :arg2 seconds
     */
    public function iWaitUntilElementExists($element, $seconds = null)
    {
        $seconds = (isset($seconds) && !empty($seconds)) ? $seconds : 10;
        $waited = 0;
        while ($waited < $seconds) {
            if ($seconds < $waited) {
                throw new \Exception(sprintf("Timeout. Failed to find %s.", $element));
            }
            $page = $this->getSession()->getPage();
            $row = $page->find('css', $element);
            if ($row) {
                return;
            }
            sleep(1);
            $waited++;
        }
        throw new \Exception("Element $element did not exist in the dom within $seconds seconds");
    }
}
