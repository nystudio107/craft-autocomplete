<?php
/**
 * Autocomplete module for Craft CMS
 *
 * Provides Twig template IDE autocomplete of Craft CMS & plugin variables
 *
 * @link      https://nystudio107.com
 * @link      https://putyourlightson.com
 * @copyright Copyright (c) nystudio107
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace nystudio107\autocomplete\generators;

use Craft;
use craft\base\Element;
use craft\web\twig\GlobalsExtension;
use nystudio107\autocomplete\base\Generator;
use nystudio107\autocomplete\events\DefineGeneratorValuesEvent;
use yii\base\Event;

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.0
 */
class AutocompleteTwigExtensionGenerator extends Generator
{
    // Constants
    // =========================================================================

    public const ELEMENT_ROUTE_EXCLUDES = [
        'matrixblock',
        'globalset',
    ];

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
            static::generateInternal();
        }
    }

    /**
     * @inheritDoc
     */
    public static function regenerate()
    {
        self::generateInternal();
    }

    // Protected Static Methods
    // =========================================================================

    /**
     * Core function that generates the autocomplete class
     */
    protected static function generateInternal()
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

        // Mix in element route variables, and override values that should be used for autocompletion
        $values = array_merge(
            $values,
            static::elementRouteVariables(),
            static::globalVariables(),
            static::overrideValues()
        );

        // Allow plugins to modify the values
        $event = new DefineGeneratorValuesEvent([
            'values' => $values,
        ]);
        Event::trigger(self::class, self::EVENT_BEFORE_GENERATE, $event);
        $values = $event->values;

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
     * Add in the element types that could be injected as route variables
     *
     * @return array
     */
    protected static function elementRouteVariables(): array
    {
        $routeVariables = [];
        $elementTypes = Craft::$app->elements->getAllElementTypes();
        foreach ($elementTypes as $elementType) {
            /* @var Element $elementType */
            $key = $elementType::refHandle();
            if (!empty($key) && !in_array($key, static::ELEMENT_ROUTE_EXCLUDES, true)) {
                $routeVariables[$key] = 'new \\' . $elementType . '()';
            }
        }

        return $routeVariables;
    }

    /**
     * Add in the global variables manually, because Craft conditionally loads the GlobalsExtension as of
     * Craft CMS 3.7.8 only for frontend routes
     *
     * @return array
     */
    protected static function globalVariables(): array
    {
        $globalVariables = [];
        // See if the GlobalsExtension class is available (Craft CMS 3.7.8 or later) and use it
        if (class_exists(GlobalsExtension::class)) {
            $globalsExtension = new GlobalsExtension();
            foreach ($globalsExtension->getGlobals() as $key => $value) {
                $globalVariables[$key] = 'new \\' . get_class($value) . '()';
            }

            return $globalVariables;
        }
        // Fall back and get the globals ourselves
        foreach (Craft::$app->getGlobals()->getAllSets() as $globalSet) {
            $globalVariables[$globalSet->handle] = 'new \\' . get_class($globalSet) . '()';
        }

        return $globalVariables;
    }

    /**
     * Override certain values that we always want hard-coded
     *
     * @return array
     */
    protected static function overrideValues(): array
    {
        return [
            // Swap in our variable in place of the `craft` variable
            'craft' => 'new \nystudio107\autocomplete\variables\AutocompleteVariable()',
            // Set the current user to a new user, so it is never `null`
            'currentUser' => 'new \craft\elements\User()',
            // Set the nonce to a blank string, as it changes on every request
            'nonce' => "''",
        ];
    }
}
