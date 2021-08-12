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
use craft\web\twig\variables\CraftVariable;

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.0
 */
class AutocompleteVariableGenerator extends Generator
{
    public CraftVariable $craftVariable;

    // Public Static Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGeneratorName(): string
    {
        return 'AutocompleteVariable';
    }

    /**
     * @inheritDoc
     */
    public function generate(): bool
    {
        if ($this->shouldRegenerateFile()) {
            return $this->generateInternal();
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function regenerate(): bool
    {
        return $this->generateInternal();
    }

    public function beforeGenerate(): void
    {
        $globals = Craft::$app->view->getTwig()->getGlobals();
        if (!($globals['craft'] instanceof CraftVariable)) {
            throw new \InvalidArgumentException("Globals do not contain 'craft' CraftVariable.");
        }

        $this->craftVariable =  $globals['craft'];
    }


    // Private Methods
    // =========================================================================

    /**
     * Core function that generates the autocomplete class
     */
    private function generateInternal(): bool
    {
        $properties = [];

        // Format the line output for each value
        foreach ($this->prepareProperties($this->craftVariable) as $key => $value) {
            $properties[] = ' * @property \\' . $value . ' $' . $key;
        }

        // Save the template with variable substitution
        return $this->saveTemplate([
            '{{ properties }}' => implode(PHP_EOL, $properties),
        ]);
    }

    private function prepareProperties(CraftVariable $craftVariable) : array
    {
        $properties = [];

        foreach ($craftVariable->getComponents() as $key => $value) {
            $type = gettype($value);
            switch ($type) {
                case 'object':
                    $className    = get_class($value);
                    $properties[$key] = $className;
                    break;

                case 'array':
                    if (isset($value['class'])) {
                        $properties[$key] = $value['class'];
                    }
                    break;

                case 'string':
                    $properties[$key] = $value;
                    break;
            }
        }

        return $properties;
    }
}
