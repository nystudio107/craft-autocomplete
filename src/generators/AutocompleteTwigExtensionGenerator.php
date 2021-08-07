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

use craft\base\ElementInterface;
use craft\elements\User;
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
        if (self::shouldRegenerateFile()) {
            static::regenerate();
        }
    }

    /**
     * @inheritDoc
     */
    public static function regenerate()
    {
        $values = [];
        /** @noinspection PhpInternalEntityUsedInspection */
        $globals = Craft::$app->view->getTwig()->getGlobals();
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

        // Ensure a current user is set
        $values['currentUser'] = 'new \\'.User::class.'()';

        // Add element types using their ref handle as the key
        $elementTypes = Craft::$app->elements->getAllElementTypes();
        foreach ($elementTypes as $elementType) {
            /** @var ElementInterface $element */
            $element = new $elementType();
            $key = $element::refHandle();
            if (!empty($key)) {
                $values[$key] = 'new \\'.$elementType.'()';
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
