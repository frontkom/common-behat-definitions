# common-behat-definitions
Common step definitions and helpers for behat

[![Packagist Downloads](https://img.shields.io/packagist/dt/frontkom/common-behat-definitions)](https://packagist.org/packages/frontkom/common-behat-definitions)


## Installation

```bash
composer require --dev frontkom/common-behat-definitions
```

Then you would probably include the contexts you are interested in, inside of your `behat.yml` file. Here is one example of including the Drupal Gutenberg Context:

```diff
       contexts:
+        - Frontkom\CommonBehatDefinitions\CommonFeatureContext
         - Drupal\DrupalExtension\Context\MinkContext
         - Drupal\DrupalExtension\Context\MarkupContext
         - Drupal\DrupalExtension\Context\MessageContext
```
