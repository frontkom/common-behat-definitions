<?php

namespace Frontkom\CommonBehatDefinitions;

use Behat\MinkExtension\Context\RawMinkContext;

class CommonFeatureContext extends RawMinkContext
{
    use JsErrorsDumper;
}
