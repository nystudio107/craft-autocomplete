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
        $components = [];
        /** @noinspection PhpInternalEntityUsedInspection */
        $globals = Craft::$app->view->getTwig()->getGlobals();
        /** @var CraftVariable $craftVariable */
        if (isset($globals['craft'])) {
            $craftVariable = $globals['craft'];
            foreach ($craftVariable->getComponents() as $key => $value) {
                $type = gettype($value);
                if ($type) {
                    if ($type === 'string') {
                        $components[$key] = $value;
                    } else {
                        $components[$key] = $type;
                    }
                }
            }
        }

        // Save the template with variable substitution
        $vars = [
        ];
        self::saveTemplate($vars);
    }
}
