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
        static::regenerate();
    }

    /**
     * @inheritDoc
     */
    public static function regenerate()
    {
        $values = [];
        /* @noinspection PhpInternalEntityUsedInspection */
        $globals = array_merge(
            Craft::$app->view->getTwig()->getGlobals(),
            Craft::$app->controller->actionParams['variables'] ?? []
        );
        foreach ($globals as $key => $value) {
            $type = gettype($value);
            switch ($type) {
                case 'object':
                    $className = get_class($value);
                    // Swap in our variable in place of the `craft` variable
                    if ($key === 'craft') {
                        $className = 'nystudio107\autocomplete\variables\AutocompleteVariable';
                    }
                    $values[$key] = 'new \\'.$className.'()';
                    break;

                case 'boolean':
                    $values[$key] = $value ? 'true' : 'false';
                    break;

                case 'integer':
                case 'double':
                    $values[$key] = $value;
                    break;

                case 'string':
                    $values[$key] = '"'.$value.'"';
                    break;

                case 'array':
                    $values[$key] = '[]';
                    break;

                case 'NULL':
                    $values[$key] = 'null';
                    break;
            }
        }

        // Format the line output for each value
        foreach ($values as $key => $value) {
            $values[$key] = '            "'.$key.'" => '.$value.',';
        }

        // Save the template with variable substitution
        self::saveTemplate([
            '{{ globals }}' => implode(PHP_EOL, $values),
        ]);
    }
}
