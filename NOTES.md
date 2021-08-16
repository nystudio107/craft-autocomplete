
### There are a few issues I see that prevent easy testing:
* You depend on `Craft::$app` in the generator classes
* static methods everywhere

### IMO the generateInternal() method has too many responsibilities
* it pulls in data (no way to test it with different data)
* it prepares the output (<-- this is what I want to test)
* it writes to file (no way to change the dest location)

## Autocomplete class
* Mixed scope: bootstrapping & actual functionality
* Why `@property-read string[] $allAutocompleteGenerators`?

## AutocompleteController class
* Autocomplete::getInstance() instead of DI


---

## Why more classes? 

The actual stuff we put into the subs wasn't testable. It was hidden in a private method of the generators.
Now, with dedicated classes (`ComponentsFormatter`, `TwigVariableFormatter`) we can test it againsts different datasets, without the need to actually write the files.