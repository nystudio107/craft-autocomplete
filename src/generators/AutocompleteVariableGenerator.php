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
class AutocompleteVariableGenerator extends Generator
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public static function getGeneratorName(): string
    {
        return 'AutocompleteVariable';
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
        $propertiesText = '';
        /** @noinspection PhpInternalEntityUsedInspection */
        $globals = Craft::$app->view->getTwig()->getGlobals();
        /** @var CraftVariable $craftVariable */
        if (isset($globals['craft'])) {
            $craftVariable = $globals['craft'];
            foreach ($craftVariable->getComponents() as $key => $value) {
                $type = gettype($value);
                switch ($type) {
                    case 'string':
                        $propertiesText .= " * @property \\{$value} \${$key}" . PHP_EOL;
                        break;

                    case 'object':
                        $className = get_class($value);
                        $propertiesText .= " * @property \\{$className} \${$key}" . PHP_EOL;
                        break;
                }

            }
        }

        // Save the template with variable substitution
        $vars = [
            '{{ properties }}' => rtrim($propertiesText, PHP_EOL),
        ];
        self::saveTemplate($vars);
    }
}
