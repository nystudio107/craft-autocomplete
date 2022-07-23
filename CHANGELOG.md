# Craft Autocomplete Changelog

All notable changes to this project will be documented in this file.

## 1.10.1 - 2022.08.23
### Changed
* Add `allow-plugins` to `composer.json` so CI can work

### Fixed
* Fixed an issue where an exception could be thrown during the bootstrap process in earlier versions of Yii2 due to `$id` not being set

## 1.10.0 - 2022.02.27
### Added
* Added compatibility with Craft CMS `^4.0.0` and Craft CMS `^3.0.0`
* Added `CODEOWNERS`

### Changed
* Code refactor/reformat

## 1.0.9 - 2021.12.23
### Changed
* Check to see if a file exists via `is_file()` before attempting to delete it with `unlink()`

## 1.0.8 - 2021.11.20
### Changed
*  Suppress errors when attempting to delete generator files that may or may not exist

## 1.0.7 - 2021.11.18
### Fixed
* Fixed a regression that would cause Autocomplete to throw an exception on < Craft CMS 3.7.8

## 1.0.6 - 2021.11.17
### Fixed
* Fixed an issue that prevented Globals from being included in the generated autocomplete class ([#8](https://github.com/nystudio107/craft-autocomplete/issues/8))

## 1.0.5 - 2021.10.30
### Added
* Added the `beforeGenerate` event to the base `Generator` class ([#7](https://github.com/nystudio107/craft-autocomplete/issues/7)).

## 1.0.4 - 2021.10.21
### Added
* Added support for plugins like Craft Commerce that add to the Craft variable via behaviors ([#6](https://github.com/nystudio107/craft-autocomplete/issues/6))

## 1.0.3 - 2021.09.22
### Changed
* Clean up AutocompleteVariableGenerator to `get()` the components to load them

### Fixed
* Fixed an error that could be thrown when a plugin was uninstalled that contained references in the Twig context ([#5](https://github.com/nystudio107/craft-autocomplete/issues/5)).

## 1.0.2 - 2021-09-02
### Changed
* Code cleanup, removed vestigial code

## 1.0.1 - 2021-08-09
### Changed
* Changed the Twig Extension Generator to only generate an autocomplete class if one does not already exist.

## 1.0.0 - 2021-08-08
### Added
* Initial release
