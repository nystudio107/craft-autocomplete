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
use craft\web\twig\variables\CraftVariable;

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
        $globalsText = '';
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
                    $globalsText.= "        '{$key}' => new \\{$className}()," . PHP_EOL;
                    break;

                case 'string':
                    $globalsText.= "        '{$key}' => '{$value}'," . PHP_EOL;
                    break;

                case 'array':
                case 'unknown type':
                    break;

                default:
                    $globalsText.= "        '{$key}' => {$value}," . PHP_EOL;
                    break;
            }
        }

        // Save the template with variable substitution
        $vars = [
            '{{ globals }}' => rtrim($globalsText, PHP_EOL),
        ];
        self::saveTemplate($vars);
    }
}
