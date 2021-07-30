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
use nystudio107\autocomplete\helpers\TypeHelper;

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
    public static function generate()
    {
        parent::generate();

        $components = [];
        $globals = Craft::$app->view->getTwig()->getGlobals();
        /** @var CraftVariable $craftVariable */
        if (isset($globals['craft'])) {
            $craftVariable = $globals['craft'];
            foreach ($craftVariable->getComponents() as $key => $value) {
                $type = TypeHelper::getType($value);
                if ($type) {
                    if ($type === 'string') {
                        $components[$key] = $value;
                    } else {
                        $components[$key] = $type;
                    }
                }
            }
        }
    }
}
