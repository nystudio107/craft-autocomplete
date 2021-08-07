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

namespace nystudio107\autocomplete\console\controllers;

use craft\helpers\Console;
use nystudio107\autocomplete\Autocomplete;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Manages autocomplete classes.
 */
class AutocompleteController extends Controller
{
    /**
     * Generates all autocomplete classes that do not already exist.
     */
    public function actionGenerate(): int
    {
        $this->stdout('Generating autocomplete classes ... ', Console::FG_YELLOW);
        Autocomplete::getInstance()->generateAutocompleteTemplates();
        $this->stdout('done' . PHP_EOL, Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Regenerates all autocomplete classes.
     */
    public function actionRegenerate(): int
    {
        $this->stdout('Regenerating autocomplete classes ... ', Console::FG_YELLOW);
        Autocomplete::getInstance()->regenerateAutocompleteTemplates();
        $this->stdout('done' . PHP_EOL, Console::FG_GREEN);

        return ExitCode::OK;
    }
}
