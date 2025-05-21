<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\MinkExtension\Context\RawMinkContext;

class GutenbergContext extends RawMinkContext
{

  /**
   * Prepend a paragraph block basically.
   *
   * @Then /^I write text "([^"]*)" as a paragraph in the gutenberg field on the page$/
   */
    public function iWriteInTheGutenbergFieldOnThePage($text)
    {
        $js = sprintf("(function(){
      var block = wp.blocks.createBlock('core/paragraph', {content: '%s'});
      wp.data.dispatch('core/editor').insertBlock(block, null);
      return block.clientId;
    })()", $text);
        $this->getSession()->evaluateScript($js);
    }

  /**
   * Step to wait for the editor to be ready.
   *
   * @Then /^I wait until gutenberg is ready$/
   */
    public function iWaitUntilGutenbergIsReady($max_wait = 10)
    {
        while (true) {
            $is_ready = $this->getSession()->evaluateScript(
                "wp &&
                wp.data &&
                wp.data.select &&
                wp.data.select('core/editor').isCleanNewPost() ||
                  wp.data.select('core/block-editor').getBlockCount() > 0"
            );
            if ($is_ready) {
                break;
            }
            sleep(1);
            $max_wait--;
            if ($max_wait <= 0) {
                throw new \Exception('Gutenberg did not load in time');
            }
        }
    }
}
