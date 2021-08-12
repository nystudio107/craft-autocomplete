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
use craft\base\Element;

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.0
 */
class AutocompleteTwigExtensionGenerator extends Generator
{
    // const
    // =========================================================================

    const ELEMENT_ROUTE_EXCLUDES = [
        'matrixblock',
        'globalset'
    ];


    public array $globals = [];

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGeneratorName(): string
    {
        return 'AutocompleteTwigExtension';
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        if ($this->shouldRegenerateFile()) {
            $this->generateInternal();
        }
    }

    /**
     * @inheritDoc
     */
    public function regenerate(): bool
    {
        return $this->generateInternal();
    }


    /**
     * @inheritDoc
     */
    public function beforeGenerate(): void
    {
        $this->globals = Craft::$app->view->getTwig()->getGlobals();
    }


    // Private Static Methods
    // =========================================================================

    /**
     * Core function that generates the autocomplete class
     */
    private function generateInternal(): bool
    {
        // Mix in element route variables, and override values that should be used for autocompletion
        $values = array_merge(
            $this->globalTwigVariables(),
            $this->elementRouteVariables(),
            $this->overrideValues()
        );

        // Format the line output for each value
        foreach ($values as $key => $value) {
            $values[$key] = "            '" . $key . "' => " . $value . ",";
        }
        // Save the template with variable substitution
        return $this->saveTemplate([
            '{{ globals }}' => implode(PHP_EOL, $values),
        ]);
    }

    private function globalTwigVariables() : array
    {
        $globals = [];

        // Iterate through the globals in the Twig context
        foreach ($this->globals as $key => $value) {
            $type = gettype($value);
            switch ($type) {
                case 'object':
                    $globals[$key] = 'new \\' . get_class($value) . '()';
                    break;

                case 'boolean':
                    $globals[$key] = $value ? 'true' : 'false';
                    break;

                case 'integer':
                case 'double':
                    $values[$key] = $value;
                    break;

                case 'string':
                    $globals[$key] = "'" . addslashes($value) . "'";
                    break;

                case 'array':
                    $globals[$key] = '[]';
                    break;

                case 'NULL':
                    $globals[$key] = 'null';
                    break;
            }
        }

        return $globals;
    }

    /**
     * Add in the element types that could be injected as route variables
     *
     * @return array
     */
    private function elementRouteVariables(): array
    {
        $routeVariables = [];
        $elementTypes = Craft::$app->elements->getAllElementTypes();
        foreach ($elementTypes as $elementType) {
            /* @var Element $elementType */
            $key = $elementType::refHandle();
            if (!empty($key) && !in_array($key, static::ELEMENT_ROUTE_EXCLUDES)) {
                $routeVariables[$key] = 'new \\' . $elementType . '()';
            }
        }

        return $routeVariables;
    }

    /**
     * Override certain values that we always want hard-coded
     *
     * @return array
     */
    private function overrideValues(): array
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
