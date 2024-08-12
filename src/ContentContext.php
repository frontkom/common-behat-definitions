<?php

namespace Frontkom\CommonBehatDefinitions;

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Class ContentContext.
 *
 * Provide Behat step-definitions for content related operations.
 */
class ContentContext extends RawDrupalContext {

  /**
   * @Then I unpublish term :name
   */
  public function iUnpublishTerm($name) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $name]);

    if ($term) {
      $term = reset($term);
      $term->setUnpublished();
      $term->save();
    }
    else {
      throw new \Exception("Term with name '$name' not found");
    }
  }

}