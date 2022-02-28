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

use Craft;
use craft\web\twig\variables\CraftVariable;
use nystudio107\autocomplete\base\Generator;
use nystudio107\autocomplete\events\DefineGeneratorValuesEvent;
use Throwable;
use yii\base\Event;

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.0
 */
class AutocompleteVariableGenerator extends Generator
{

    // Constants
    // =========================================================================

    const BEHAVIOR_PROPERTY_EXCLUDES = [
        'owner',
    ];

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
        static::generateInternal();
    }

    // Private Static Methods
    // =========================================================================

    /**
     * Core function that generates the autocomplete class
     */
    private static function generateInternal()
    {
        $values = [];
        /* @noinspection PhpInternalEntityUsedInspection */
        $globals = Craft::$app->view->getTwig()->getGlobals();
        /* @var CraftVariable $craftVariable */
        if (isset($globals['craft'])) {
            $craftVariable = $globals['craft'];
            // Handle the components
            foreach ($craftVariable->getComponents() as $key => $value) {
                try {
                    $values[$key] = get_class($craftVariable->get($key));
                } catch (Throwable $e) {
                    // That's okay
                }
            }
            // Handle the behaviors
            foreach ($craftVariable->getBehaviors() as $behavior) {
                $properties = get_object_vars($behavior);
                foreach ($properties as $key => $value) {
                    try {
                        if (is_object($value) && !in_array($key, static::BEHAVIOR_PROPERTY_EXCLUDES, true)) {
                            $values[$key] = get_class($value);
                        }
                    } catch (Throwable $e) {
                        // That's okay
                    }
                }
            }
        }

        // Allow plugins to modify the values
        $event = new DefineGeneratorValuesEvent([
            'values' => $values,
        ]);
        Event::trigger(self::class, self::EVENT_BEFORE_GENERATE, $event);
        $values = $event->values;

        // Format the line output for each value
        foreach ($values as $key => $value) {
            $values[$key] = ' * @property \\' . $value . ' $' . $key;
        }

        // Save the template with variable substitution
        self::saveTemplate([
            '{{ properties }}' => implode(PHP_EOL, $values),
        ]);
    }
}
