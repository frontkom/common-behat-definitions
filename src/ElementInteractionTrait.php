<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\Mink\Exception\UnsupportedDriverActionException;

trait ElementInteractionTrait
{

  /**
   * A basic element click.
   *
   * @Then I click the :arg1 element
   */
  public function iClickTheElement($selector)
  {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', $selector);

    if (empty($element)) {
      throw new \Exception("No html element found for the selector ('$selector')");
    }
    $element->click();
  }

  /**
   * Optionally click an element.
   *
   * This is useful if for example you want to click the element in some
   * environments, or given some conditions, but not in others, and you don't
   * really care if it succeeds or not.
   *
   * @Then I click the :arg1 element if I have to
   */
  public function iClickTheElementIfiHaveTo($selector)
  {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', $selector);

    if (empty($element)) {
      // Then I dont have to.
      return;
    }
    try {
      $element->click();
    }
    catch (UnsupportedDriverActionException $e) {
      // This is totally fine.
    }
  }
}
