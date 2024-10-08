<?php

namespace Frontkom\CommonBehatDefinitions;

trait ViewPortTrait {

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

}
