<?php
/**
 * Autocomplete plugin for Craft CMS 3.x
 *
 * Provides Twig template IDE autocomplete of Craft CMS & plugin variables
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2021 nystudio107
 */

namespace nystudio107\autocomplete\console\controllers;

use craft\helpers\Console;
use nystudio107\autocomplete\Autocomplete;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Manages autocomplete.
 */
class AutocompleteController extends Controller
{
    /**
     * Regenerates all autocomplete templates.
     */
    public function actionRegenerate()
    {
        $this->stdout('Regenerating all autocomplete templates ... ', Console::FG_YELLOW);

        Autocomplete::getInstance()->generateAutocompleteTemplates();

        $this->stdout('done' . PHP_EOL, Console::FG_GREEN);

        return ExitCode::OK;
    }
}
