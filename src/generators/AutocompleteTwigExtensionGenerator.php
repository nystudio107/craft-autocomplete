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

use craft\elements\GlobalSet;
use craft\elements\MatrixBlock;
use craft\services\Elements;
use nystudio107\autocomplete\base\Generator;

use Craft;
use craft\base\Element;
use nystudio107\autocomplete\generators\formatter\TwigVariableFormatter;
use yii\base\BaseObject;

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
        MatrixBlock::class,
        GlobalSet::class
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

        $elementTypes = \Craft::$app->getElements()->getAllElementTypes();
        $formatter = new TwigVariableFormatter(
            array_diff($elementTypes, self::ELEMENT_ROUTE_EXCLUDES),
            $this->globals
        );

        // Mix in element route variables, and override values that should be used for autocompletion
        $values = array_merge(
            $formatter->globalTwigVariables(),
            $formatter->elementRouteVariables(),
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
