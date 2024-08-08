<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Class TrackingContext.
 *
 * Provide Behat step-definitions for operations related to tracking like GTM.
 */
class TrackingContext extends RawDrupalContext {

  /**
   * Check if commerce GTM event is fired with correct items.
   *
   * @Then A google event should be fired with name :name and items:
   */
  public function aGoogleEventShouldBeFiredWithItems($name, TableNode $nodesTable = NULL) {
    $session = $this->minkContext->getSession();
    $script = "return dataLayer;";
    $dataLayer = $session->evaluateScript($script);

    $events = array_filter($dataLayer, function ($element) {
      return isset($element[0]) && $element[0] === 'event';
    });

    $searchedEvent = NULL;
    foreach ($events as $event) {
      if ($event[1] === $name) {
        $searchedEvent = $event;
      }
    }

    if (!$searchedEvent) {
      throw new \Exception("GTM event with name '$name' is not present in the dataLayer.");
    }

    if (!$nodesTable) {
      return;
    }

    if (!isset($searchedEvent[2])) {
      throw new \Exception("GTM event with name '$name' has no parameters.");
    }

    if (!isset($searchedEvent[2]['items'])) {
      throw new \Exception("GTM event with name '$name' has no items.");
    }

    $eventItems = $searchedEvent[2]['items'];
    foreach ($nodesTable->getHash() as $nodeHash) {
      // Try to find the search event with given params by comparing arrays.
      $eventFound = FALSE;
      foreach ($eventItems as $eventItem) {
        $differences = array_diff($nodeHash, $eventItem);
        if ($differences === []) {
          $eventFound = TRUE;
        }
      }

      if (!$eventFound) {
        throw new \Exception("GTM event with name '$name' does not contain one of the given items.");
      }
    }
  }

}