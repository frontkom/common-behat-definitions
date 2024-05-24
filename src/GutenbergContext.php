<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\MinkExtension\Context\RawMinkContext;

class GutenbergContext extends RawMinkContext {

  /**
   * Prepend a paragraph block basically.
   *
   * @Then /^I write text "([^"]*)" as a paragraph in the gutenberg field on the page$/
   */
  public function iWriteInTheGutenbergFieldOnThePage($text) {
    $js = sprintf("(function(){
      var block = wp.blocks.createBlock('core/paragraph', {content: '%s'});
      wp.data.dispatch('core/editor').insertBlock(block, null);
      return block.clientId;
    })()", $text);
    $this->getSession()->evaluateScript($js);
  }

}
