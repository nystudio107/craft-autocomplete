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
use craft\web\twig\variables\CraftVariable;
use nystudio107\autocomplete\base\Generator;
use nystudio107\autocomplete\events\DefineGeneratorValuesEvent;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
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

    public const BEHAVIOR_PROPERTY_EXCLUDES = [
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

    // Protected Static Methods
    // =========================================================================

    /**
     * Core function that generates the autocomplete class
     */
    protected static function generateInternal()
    {
        $properties = [];
        $methods = [];
        /* @noinspection PhpInternalEntityUsedInspection */
        $globals = Craft::$app->view->getTwig()->getGlobals();
        /* @var CraftVariable $craftVariable */
        if (isset($globals['craft'])) {
            $craftVariable = $globals['craft'];
            // Handle the components
            foreach ($craftVariable->getComponents() as $key => $value) {
                try {
                    $properties[$key] = get_class($craftVariable->get($key));
                } catch (Throwable $e) {
                    // That's okay
                }
            }
            // Handle the behaviors
            foreach ($craftVariable->getBehaviors() as $behavior) {
                try {
                    $reflect = new ReflectionClass($behavior);
                    // Properties
                    foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectProp) {
                        // Property name
                        $reflectPropName = $reflectProp->getName();
                        // Ensure the property exists only for this class and not any parent class
                        if (property_exists(get_parent_class($behavior), $reflectPropName)) {
                            continue;
                        }
                        // Do it this way because getType() reflection method is >= PHP 7.4
                        $reflectPropType = gettype($behavior->$reflectPropName);
                        switch ($reflectPropType) {
                            case 'object':
                                $properties[$reflectPropName] = get_class($behavior->$reflectPropName);
                                break;
                            default:
                                $properties[$reflectPropName] = $reflectPropType;
                                break;
                        }
                    }
                    // Methods
                    foreach ($reflect->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectMethod) {
                        // Method name
                        $reflectMethodName = $reflectMethod->getName();
                        // Ensure the method exists only for this class and not any parent class
                        if (method_exists(get_parent_class($behavior), $reflectMethodName)) {
                            continue;
                        }
                        // Method return type
                        $methodReturn = '';
                        $reflectMethodReturnType = $reflectMethod->getReturnType();
                        if ($reflectMethodReturnType instanceof ReflectionNamedType) {
                            $methodReturn = ': ' . $reflectMethodReturnType->getName();
                        }
                        // Method parameters
                        $methodParams = [];
                        foreach ($reflectMethod->getParameters() as $methodParam) {
                            $paramType = '';
                            $methodParamType = $methodParam->getType();
                            if ($methodParamType) {
                                $paramType = $methodParamType . ' ';
                            }
                            $methodParams[] = $paramType . '$' . $methodParam->getName();
                        }
                        $methods[$reflectMethodName] = '(' . implode(', ', $methodParams) . ')' . $methodReturn;
                    }
                } catch (\ReflectionException $e) {
                }
            }
        }

        // Allow plugins to modify the values
        $event = new DefineGeneratorValuesEvent([
            'values' => $properties,
        ]);
        Event::trigger(self::class, self::EVENT_BEFORE_GENERATE, $event);
        $properties = $event->values;

        // Format the line output for each property
        foreach ($properties as $key => $value) {
            $properties[$key] = ' * @property \\' . $value . ' $' . $key;
        }
        // Format the line output for each method
        foreach ($methods as $key => $value) {
            $methods[$key] = ' * @method ' . $key . $value;
        }

        // Save the template with variable substitution
        self::saveTemplate([
            '{{ properties }}' => implode(PHP_EOL, array_merge($properties, $methods)),
        ]);
    }
}
