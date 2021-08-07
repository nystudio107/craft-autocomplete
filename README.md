# Autocomplete for Craft CMS 3.x

Provides Twig template IDE autocompletion for Craft CMS and plugin/module variables.

Works with PhpStorm provided the [Symfony plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin
) is installed. VSCode currently does not support intellisense for Twig extensions.

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Usage

Install the package using composer.

```
composer require nystudio107/craft-autocomplete
```

Ensure that the [PhpStorm Symfony plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin
) is installed and enabled.  
    
Visit the Craft site on which the package is installed to generate the autocomplete classes, or run the following console command.

```shell
php craft autocomplete/generate
```

Once your IDE indexes the autocomplete classes, autocompletion for Craft and all plugins and modules will immediately become available in your Twig templates.

![screenshot](https://user-images.githubusercontent.com/57572400/125784167-618830ae-e475-4faf-81d3-194ad7ce3a08.png)

## Regenerating Autocomplete Classes

The autocomplete classes are regenerated every time you install or uninstall a plugin. If you manually add a plugin/module that provides its own variables, you can regenerate the autocomplete classes by running the following console command.

```shell
php craft autocomplete/regenerate
```

---

Brought to you by [nystudio107](https://nystudio107.com) and [PutYourLightsOn](https://putyourlightson.com/).
