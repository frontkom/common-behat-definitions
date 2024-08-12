<?php

namespace Frontkom\CommonBehatDefinitions;

use Drupal\Core\Url;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Class UserContext.
 *
 * Provide Behat step-definitions for user related operations.
 */
class UserContext extends RawDrupalContext {

  /**
   * Helper to edit user.
   *
   * @When I edit user with email :mail
   */
  public function iEditUser($mail) {
    $id = $this->getUserIdByMail($mail);
    $this->visitPath('user/' . $id . '/edit');
  }

  /**
   * Helper to visit a route with user param.
   *
   * @When I visit route :route with user parameter having mail :mail
   */
  public function iVisitRouteForUser($route, $mail) {
    $id = $this->getUserIdByMail($mail);
    $url = Url::fromRoute($route, ['user' => $id]);
    $this->visitPath($url->toString());
  }

  /**
   * Helper to get user ID.
   */
  private function getUserIdByMail($mail) {
    $users = \Drupal::entityTypeManager()->getStorage('user')
      ->getQuery()
      ->condition('mail', $mail)
      ->accessCheck()
      ->execute();
    if (empty($users)) {
      throw new \Exception('No users found with email ' . $mail);
    }
    if (count($users) > 1) {
      throw new \Exception('More than 1 user found with email ' . $mail);
    }
    return reset($users);
  }

}