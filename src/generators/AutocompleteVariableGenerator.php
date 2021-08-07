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
    public static function generate()
    {
        parent::generate();

        $variables = [];
        $globals = Craft::$app->view->getTwig()->getGlobals();
        foreach ($globals as $key => $value) {
            $type = gettype($value);
            if ($type) {
                $variables[$key] = $type;
            }
        }
    }
}
