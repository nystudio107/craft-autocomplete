# Autocomplete for Craft CMS 3.x

Provides Twig template IDE autocomplete of Craft CMS & plugin variables.

Currently works with **PhpStorm only**, as VSCode does not support intellisense for Twig extensions.

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Usage

Install the package using composer.

```
composer require nystudio107/craft-autocomplete
```

Ensure that the Symfony plugin is installed and enabled in PhpStorm:  
https://plugins.jetbrains.com/plugin/7219-symfony-plugin
    
Visit the Craft site on which the package is installed to generate the autocomplete classes, or run the console command.

```shell
php craft autocomplete/generate
```

Once your IDE indexes the files, autocompletion for Craft and supported plugins will immediately become available in your Twig templates.

![screenshot](https://user-images.githubusercontent.com/57572400/125784167-618830ae-e475-4faf-81d3-194ad7ce3a08.png)

## Regenerating

The autocomplete classes are regenerated every time you install or uninstall a plugin. If you manually add a plugin/module that provides its own variables, you can regenerate the autocomplete classes by running the following console command.

```shell
php craft autocomplete/generate
```

---

Brought to you by [nystudio107](https://nystudio107.com) and [PutYourLightsOn](https://putyourlightson.com/).
