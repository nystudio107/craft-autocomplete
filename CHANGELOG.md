# Craft Autocomplete Changelog

All notable changes to this project will be documented in this file.

## 1.0.3 - 2021.09.22
### Changed
* Clean up AutocompleteVariableGenerator to `get()` the components to load them

### Fixed
* Fixed an error that could be thrown when a plugin was uninstalled that contained references in the Twig context. ([#5](https://github.com/nystudio107/craft-autocomplete/issues/5))

## 1.0.2 - 2021-09-02
### Changed
* Code cleanup, removed vestigial code

## 1.0.1 - 2021-08-09
### Changed
* Changed the Twig Extension Generator to only generate an autocomplete class if one does not already exist.

## 1.0.0 - 2021-08-08
### Added
* Initial release
