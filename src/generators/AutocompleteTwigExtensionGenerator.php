<?php
/**
 * Autocomplete plugin for Craft CMS 3.x
 *
 * Provides Twig template IDE autocomplete of Craft CMS & plugin variables
 *
 * @link      https://nystudio107.com
 * @link      https://putyourlightson.com
 * @copyright Copyright (c) 2021 nystudio107
 * @copyright Copyright (c) 2021 PutYourLightsOn
 */

namespace nystudio107\autocomplete\generators;

use nystudio107\autocomplete\base\Generator;

use Craft;

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.0
 */
class AutocompleteTwigExtensionGenerator extends Generator
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public static function getGeneratorName(): string
    {
        return 'AutocompleteTwigExtension';
    }

    /**
     * @inheritDoc
     */
    public static function generate()
    {
        // We always regenerate, to be context-sensitive based on the last page that was loaded/rendered
        self::generateInternal();
    }

    /**
     * @inheritDoc
     */
    public static function regenerate()
    {
        self::generateInternal();
    }

    // Private Static Methods
    // =========================================================================

    /**
     * Core function that generates the autocomplete class
     */
    private static function generateInternal()
    {
        $values = [];
        // Iterate through the globals in the Twig context
        /* @noinspection PhpInternalEntityUsedInspection */
        $globals = Craft::$app->view->getTwig()->getGlobals();
        foreach ($globals as $key => $value) {
            $type = gettype($value);
            switch ($type) {
                case 'object':
                    $values[$key] = 'new \\' . get_class($value) . '()';
                    break;

                case 'boolean':
                    $values[$key] = $value ? 'true' : 'false';
                    break;

                case 'integer':
                case 'double':
                    $values[$key] = $value;
                    break;

                case 'string':
                    $values[$key] = "'" . addslashes($value) . "'";
                    break;

                case 'array':
                    $values[$key] = '[]';
                    break;

                case 'NULL':
                    $values[$key] = 'null';
                    break;
            }
        }
        // Override values that should be used for autocompletion
        static::overrideValues($values);
        // Format the line output for each value
        foreach ($values as $key => $value) {
            $values[$key] = "            '" . $key . "' => " . $value . ",";
        }
        // Save the template with variable substitution
        self::saveTemplate([
            '{{ globals }}' => implode(PHP_EOL, $values),
        ]);
    }

    /**
     * Override certain values that we always want hard-coded
     *
     * @param array $values
     */
    private static function overrideValues(array &$values)
    {
        // Swap in our variable in place of the `craft` variable
        $values['craft'] = 'new \nystudio107\autocomplete\variables\AutocompleteVariable()';

        // Set the current user to a new user, so it is never `null`
        $values['currentUser'] = 'new \craft\elements\User()';

        // Set the nonce to a blank string, as it changes on every request
        $values['nonce'] = "''";
    }
}
