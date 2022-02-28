[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nystudio107/craft-autocomplete/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/nystudio107/craft-autocomplete/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/nystudio107/craft-autocomplete/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/nystudio107/craft-autocomplete/?branch=develop) [![Build Status](https://scrutinizer-ci.com/g/nystudio107/craft-autocomplete/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/nystudio107/craft-autocomplete/build-status/develop) [![Code Intelligence Status](https://scrutinizer-ci.com/g/nystudio107/craft-autocomplete/badges/code-intelligence.svg?b=develop)](https://scrutinizer-ci.com/code-intelligence)

# Autocomplete for Craft CMS 3.x & 4.x

Provides Twig template IDE autocompletion for Craft CMS and plugin/module variables and element types.

Works with PhpStorm provided the [Symfony Support plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin
) is installed. VSCode currently does not support intellisense for Twig extensions.

> While Craft [3.7.8](https://github.com/craftcms/cms/blob/develop/CHANGELOG.md#378---2021-08-06) added autocompletion for Craft’s global Twig variables, this does not include autocompletion for plugins and modules that provide their own variables or element types.

![demo](https://user-images.githubusercontent.com/57572400/126911028-7d7d06dd-c60f-42b9-ae42-95d5f078a229.gif)

## Requirements

This package requires Craft CMS ^3.0.0 or Craft CMS ^4.0.0.

## Usage

1. Install the package using composer, adding it to `require-dev`:

```shell
composer require nystudio107/craft-autocomplete --dev
```

2. Ensure that the [Symfony Support plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin) for PhpStorm is installed and enabled by checking the **Enabled for Project** checkbox in the Symfony plugin settings.  

3. Ensure that `devMode` is enabled.

4. Visit the Craft site on which the package is installed to generate the autocomplete classes in `storage/runtime/compiled_classes/` or run the following console command.

```shell
php craft autocomplete/generate
```

Once your IDE indexes the autocomplete classes, autocompletion for Craft and all plugins and modules will immediately become available in your Twig templates.

![screenshot](https://user-images.githubusercontent.com/57572400/125784167-618830ae-e475-4faf-81d3-194ad7ce3a08.png)

Additionally, autocompletion for element types provided by both Craft and plugins/modules is available, for example: `asset`, `entry`, `category`, `tag`, `user`, `product` (if Craft Commerce is installed), etc.

**N.B.:** If you are using a Docker-ized setup, ensure that `storage/runtime/compiled_classes/` is bind mounted on your client machine, so your IDE can find the classes to index them

## Regenerating Autocomplete Classes

The autocomplete classes are all generated any time Craft executes (whether via frontend request or via CLI), if they do not yet exist.

The autocomplete classes are all regenerated every time you install or uninstall a plugin.

If you manually add a plugin or module that registers variables on the Craft global variable, you can force the regeneratation of the autocomplete classes by running the following console command.

```shell
php craft autocomplete/regenerate
```

...or since the autocomplete classes are automatically regenerated if they don’t exist, you can clear the Runtime caches with:

```shell
php craft clear-caches/temp-files
```

## Extending

You can extend the values that a `Generator` class adds using the `EVENT_BEFORE_GENERATE` event.

```php
use nystudio107\autocomplete\events\DefineGeneratorValuesEvent;
use nystudio107\autocomplete\generators\AutocompleteTwigExtensionGenerator;
use yii\base\Event;

Event::on(AutocompleteTwigExtensionGenerator::class,
    AutocompleteTwigExtensionGenerator::EVENT_BEFORE_GENERATE,
    function(DefineGeneratorValuesEvent $event) {
        $event->values['myVariable'] = 'value';
    }
);
```

In addition to the provided autocomplete generator types, you can write your own by implementing the `GeneratorInterface` class or extending the abstract `Generator` class (recommended).

```php
<?php
namespace vendor\package;

use nystudio107\autocomplete\base\Generator;

class MyAutocompleteGenerator extends Generator
{
    // Override base methods
}
```

To register your generator type, listen for the `EVENT_REGISTER_AUTOCOMPLETE_GENERATORS` event and add your class to the `types` property.

```php
use nystudio107\autocomplete\Autocomplete;
use craft\events\RegisterComponentTypesEvent;
use yii\base\Event;

Event::on(Autocomplete::class,
    Autocomplete::EVENT_REGISTER_AUTOCOMPLETE_GENERATORS,
    function(RegisterComponentTypesEvent $event) {
        $event->types[] = MyAutocompleteGenerator::class;
    }
);
```

See the included generators for guidance on how to create your own.

## How It Works

On the quest for autocomplete in the PhpStorm IDE, Andrew wrote an article years ago entitled [Auto-Complete Craft CMS 3 APIs in Twig with PhpStorm](https://nystudio107.com/blog/auto-complete-craft-cms-3-apis-in-twig-with-phpstorm)

This worked on principles similar to how Craft Autocomplete works, but it was a manual process.  
Ben and Andrew thought they could do better.

### Bootstrapping Yii2 Extension

This package is a Yii2 extension (and a module) that [bootstraps itself](https://www.yiiframework.com/doc/guide/2.0/en/structure-extensions#bootstrapping-classes).

This means that it’s automatically loaded with Craft, without you having to install it or configure it in any way.

It only ever does anything provided that `devMode` is enabled, so it’s fine to keep it installed on production.

### The Generated Autocomplete Classes

All Craft Autocomplete does is generate source code files, very similar to how Craft itself generates a [CustomFieldBehavior](https://github.com/craftcms/cms/blob/96fc9a3f2fc7caabc44d12d786dea2a39ffa4b62/src/Craft.php#L232) class in `storage/runtime/compiled_classes`

The code that is generated by Craft Autocomplete is never run, however. It exists just to allow your IDE to index it for autocomplete purposes.

During the bootstrapping process, the package generates two classes, `AutocompleteTwigExtension` and `AutocompleteVariable`, if they do not already exist or if a Craft plugin was just installed or uninstalled.

The `AutocompleteTwigExtension` class is generated by evaluating all the Twig globals that have been registered. The `AutocompleteVariable` class is generated by dynamically evaluating the global Craft variable, including any variables that have been registered on it (by plugins and modules).

Here’s an example of what the files it generates might look like, stored in `storage/runtime/compiled_classes`:

`AutocompleteVariable.php`:

```php
<?php

namespace nystudio107\autocomplete\variables;

/**
 * Generated by Craft Autocomplete
 *
 * @property \craft\web\twig\variables\Cp $cp
 * @property \craft\web\twig\variables\Io $io
 * @property \craft\web\twig\variables\Routes $routes
 * ...
 * @property \modules\sitemodule\variables\SiteVariable $site
 * @property \nystudio107\imageoptimize\variables\ImageOptimizeVariable $imageOptimize
 * @property \putyourlightson\blitz\variables\BlitzVariable
 */
class AutocompleteVariable extends \craft\web\twig\variables\CraftVariable
{
}
```

`AutocompleteVariable.php`:

```php
<?php

namespace nystudio107\autocomplete\twigextensions;

/**
 * Generated by Craft Autocomplete
 */
class AutocompleteTwigExtension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{
    public function getGlobals(): array
    {
        return [
            'craft' => new \nystudio107\autocomplete\variables\AutocompleteVariable(),
            'currentSite' => new \craft\models\Site(),
            'currentUser' => new \craft\elements\User(),
            // ...
            'seomatic' => new \nystudio107\seomatic\variables\SeomaticVariable(),
            'sprig' => new \putyourlightson\sprig\variables\SprigVariable(),
        ];
    }
}
```

### The Symfony Support Plugin for PhpStorm

The other half of the equation is on the PhpStorm IDE end of things, provided by the [Symfony Support plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin
).

One of the things this PhpStorm plugin (written in Java) does is parse your code for Twig extensions that add global variables.

It’s important to note that it does not actually evaluate any PHP code. Instead, it parses all Twig extension PHP classes looking for a `getGlobals()` method that returns a key/value array via a `return []` statement and makes their values available as global variables in Twig for autocompletion.

The reason this has never "just worked" in the history of Craft CMS [up until version 3.7.8](https://github.com/craftcms/cms/commit/1718c95271d62d3966f2131d4b7620cc0a6191fe) is that Craft returned an array as a variable, rather than as a static key/value pair array, so the Symfony plugin could not parse it.

If a plugin or module (or even Craft pre 3.7.8) does not return a key/value array directly then autocompletion simply will not work (Andrew had to discover this by [source-diving the Symfony Support plugin](https://github.com/Haehnchen/idea-php-symfony2-plugin/blob/master/src/main/java/fr/adrienbrault/idea/symfony2plugin/templating/variable/collector/GlobalExtensionVariableCollector.java#L19)):

```java
/**
 * @author Daniel Espendiller <daniel@espendiller.net>
 */
public class GlobalExtensionVariableCollector implements TwigFileVariableCollector {
    @Override
    public void collectPsiVariables(@NotNull TwigFileVariableCollectorParameter parameter, @NotNull Map<String, PsiVariable> variables) {
        for(PhpClass phpClass : TwigUtil.getTwigExtensionClasses(parameter.getProject())) {
            if(!PhpUnitUtil.isPhpUnitTestFile(phpClass.getContainingFile())) {
                Method method = phpClass.findMethodByName("getGlobals");
                if(method != null) {
                    Collection<PhpReturn> phpReturns = PsiTreeUtil.findChildrenOfType(method, PhpReturn.class);
                    for(PhpReturn phpReturn: phpReturns) {
                        PhpPsiElement returnPsiElement = phpReturn.getFirstPsiChild();
                        if(returnPsiElement instanceof ArrayCreationExpression) {
                            variables.putAll(PhpMethodVariableResolveUtil.getTypesOnArrayHash((ArrayCreationExpression) returnPsiElement));
                        }
                    }
                }
            }
        }
    }
}
```


Once PhpStorm has indexed these two classes, autocompletion for Craft and all plugins and modules immediately becomes available in your Twig templates, just like magic!

### Hat tip

Hat tip to Oliver Stark for his work on [ostark/craft-prompter](https://mobile.twitter.com/o_stark/status/1415743590005944328).

---

Brought to you by [nystudio107](https://nystudio107.com) and [PutYourLightsOn](https://putyourlightson.com/).
