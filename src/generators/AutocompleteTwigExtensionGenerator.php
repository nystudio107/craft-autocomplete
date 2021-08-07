<?php
/**
 * Autocomplete plugin for Craft CMS 3.x
 *
 * Provides Twig template IDE autocomplete of Craft CMS & plugin variables
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2021 nystudio107
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
    public static function generate(): void
    {
        if (self::shouldRegenerateFile()) {
            static::regenerate();
        }
    }

    /**
     * @inheritDoc
     */
    public static function regenerate(): void
    {
        $globalsArray = [];
        /** @noinspection PhpInternalEntityUsedInspection */
        $globals = Craft::$app->view->getTwig()->getGlobals();
        foreach ($globals as $key => $value) {
            $type = gettype($value);
            switch ($type) {
                case 'object':
                    $className = get_class($value);
                    // Swap in our variable in place of the 'craft' variable
                    if ($key === 'craft') {
                        $className = 'nystudio107\autocomplete\variables\AutocompleteVariable';
                    }
                    $globalsArray[$key] = "new \\$className()";
                    break;

                case 'boolean':
                    $globalsArray[$key] = $value ? "true" : "false";
                    break;

                case 'integer':
                case 'double':
                    $globalsArray[$key] = "$value";
                    break;

                case 'string':
                    $globalsArray[$key] = "'$value'";
                    break;

                case 'array':
                    $globalsArray[$key] = "[]";
                    break;

                case 'NULL':
                    $globalsArray[$key] = "null";
                    break;
            }
        }

        foreach ($globalsArray as $key => $value) {
            $globalsArray[$key] = "            '$key' => $value,";
        }

        // Save the template with variable substitution
        self::saveTemplate([
            '{{ globals }}' => implode(PHP_EOL, $globalsArray),
        ]);
    }
}
