<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Driver\Selenium2Driver;

/**
 * Provides AfterStep that dumps any JS errors that appear during given step.
 */
trait JsErrorsDumper {

  /**
   * Checks that the step did not trigger any JS errors.
   *
   * @AfterStep
   */
  public function lookForJsErrors(AfterStepScope $scope) {
    // No steps, nothing to do.
    if (!$scope->getStep()) {
      return;
    }
    $driver = $this->getSession()->getDriver();
    if (!($driver instanceof Selenium2Driver)) {
      // Only works in browsers.
      return;
    }
    // If the webdriver session has not started, probably means we are not
    // navigating in a context where we can actually evaluate any scripts at
    // all. This AfterStep will also trigger on steps in a background, for
    // example stuff like "Given a node with title something", which of course
    // will not let us have a browser window to check if there are any errors,
    // even if the driver of the session technically is a Selenium2Driver.
    if (!$driver->getWebDriverSession()) {
      return;
    }

    try {
      $errors = $this->getSession()->evaluateScript("return window.jsErrors");
    } catch (\Exception $e) {
      // output where the error occurred for debugging purposes
      echo $this->scenarioData;
      throw $e;
    }

    if (!$errors || empty($errors)) {
      return;
    }

    $file = sprintf("%s:%d", $scope->getFeature()->getFile(), $scope->getStep()->getLine());
    $message = sprintf("Found %d javascript error%s", count($errors), count($errors) > 0 ? 's' : '');

    echo '-------------------------------------------------------------' . PHP_EOL;
    echo $file . PHP_EOL;
    echo $message . PHP_EOL;
    echo '-------------------------------------------------------------' . PHP_EOL;

    foreach ($errors as $index => $error) {
      echo sprintf("   #%d: %s", $index, $error) . PHP_EOL;
    }

    throw new \Exception($message);
  }

}
