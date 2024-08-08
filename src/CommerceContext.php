<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\Gherkin\Node\TableNode;
use Drupal\commerce_promotion\Entity\Coupon;
use Drupal\commerce_promotion\Entity\Promotion;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Class CommerceContext.
 *
 * Provide Behat step-definitions for common Commerce functionalities.
 */
class CommerceContext extends RawDrupalContext {

  /**
   * Generate coupons.
   *
   * @Given coupons:
   */
  public function createCoupons(TableNode $nodesTable) {
    foreach ($nodesTable->getHash() as $nodeHash) {
      $coupon = (object) $nodeHash;
      /** @var \Drupal\commerce_promotion\Entity\PromotionInterface $promotion */
      $promotion = $this->getPromotionByName($coupon->promotion);
      if ($promotion) {
        $coupon_saved = $this->couponCreate($coupon);
        $coupon_loaded = Coupon::load($coupon_saved->id);
        $promotion->get('coupons')->appendItem($coupon_loaded);
        $promotion->save();
      }
      else {
        throw new \Exception("No parent promotion found.");
      }
    }
  }

  /**
   * Load promotion by name.
   */
  public function getPromotionByName($promotion_name) {
    foreach ($this->promotions as $promotion) {
      $loaded_promotion = Promotion::load($promotion->id);
      if ($loaded_promotion->label() === $promotion_name) {
        return $loaded_promotion;
      }
    }

    return NULL;
  }

  /**
   * Create coupon.
   */
  public function couponCreate($coupon) {
    $saved = $this->getDriver()->createEntity('commerce_promotion_coupon', $coupon);
    return $saved;
  }

  /**
   * Visit promotion edit page.
   *
   * @Then I visit promotion :title edit page
   */
  public function iVisitPromotionEditPage($title) {
    $promotions = \Drupal::entityTypeManager()->getStorage('commerce_promotion')
      ->loadByProperties([
        'name' => $title,
      ]);
    if (count($promotions) !== 1) {
      throw new \Exception('Expected 1 promotion with title ' . $title . ' but found ' . count($promotions));
    }

    $id = reset($promotions)->id();
    $this->getSession()->visit($this->locatePath("promotion/$id/edit"));
  }

  /**
   * Remove promotions by titles.
   *
   * @Then I remove promotions :titles
   */
  public function iRemovePromotions($titles) {
    $promotions = \Drupal::entityTypeManager()->getStorage('commerce_promotion')
      ->loadByProperties([
        'name' => explode(', ', $titles),
      ]);
    foreach ($promotions as $promotion) {
      $promotion->delete();
    }
  }

  /**
   * @Then /^a product with SKU "([^"]*)" should exist$/
   */
  public function aProductWithSKUShouldExist($sku) {
    /** @var \Drupal\commerce_product\ProductVariationStorage $variation_storage */
    $variation_storage = \Drupal::entityTypeManager()->getStorage('commerce_product_variation');
    $product_variations = $variation_storage->loadBySku($sku);
    if (empty($product_variations)) {
      throw new \Exception('No variation found with SKU ' . $sku);
    }
  }

  /**
   * @Given I set :value exchange rates
   */
  public function setManualExchangeRates($value) {
    $currency_storage = \Drupal::service('entity_type.manager')->getStorage('commerce_currency');
    $currencies = $currency_storage->loadMultiple();
    $currencies = array_keys($currencies);
    $database = \Drupal::database();
    $time = time();

    foreach ($currencies as $currency) {
      $query = $database->insert('commerce_exchanger_latest_rates')
        ->fields([
          'exchanger',
          'source',
          'target',
          'value',
          'timestamp',
          'manual',
        ]);

      $query->values([
        'exchanger' => 'manual',
        'source' => $currency,
        'target' => 'NOK',
        'value' => $value,
        'timestamp' => $time,
        // A weird value just to identify records to remove in AfterScenario.
        'manual' => '666666',
      ]);
      $query->execute();
    }
  }

  /**
   * Remove exchange rates.
   *
   * @AfterScenario
   */
  public function cleanExchangeRates() {
    $database = \Drupal::database();
    $database->delete('commerce_exchanger_latest_rates')
      ->condition('manual', '666666', '=')
      ->execute();
  }

}