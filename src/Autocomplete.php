<?php
/**
 * Autocomplete plugin for Craft CMS 3.x
 *
 * Provides Twig template IDE autocomplete of Craft CMS & plugin variables
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2021 nystudio107
 */

namespace nystudio107\autocomplete;

use Craft;
use craft\console\Application as CraftConsoleApp;
use craft\services\Plugins;
use craft\web\Application as CraftWebApp;
use yii\base\Application as YiiApp;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\Event;

/**
 * Class Autocomplete
 *
 * @author    nystudio107
 * @package   Autocomplete
 * @since     1.0.0
 */
class Autocomplete extends Component implements BootstrapInterface
{
    /**
     * Bootstraps the extension
     *
     * @param YiiApp $app
     */
    public function bootstrap($app)
    {
        // Make sure it's Craft
        if (!($app instanceof CraftWebApp || $app instanceof CraftConsoleApp)) {
            return;
        }

        // Make sure we're in devMode
        if (!Craft::$app->config->general->devMode) {
            return;
        }

        $this->registerEventHandlers();
    }

    /**
     * Registers our event handlers
     */
    public function registerEventHandlers()
    {
        Event::on(Plugins::class,Plugins::EVENT_AFTER_INSTALL_PLUGIN, [$this, 'generateAutocompleteVariable']);
        Event::on(Plugins::class,Plugins::EVENT_AFTER_UNINSTALL_PLUGIN, [$this, 'generateAutocompleteVariable']);
        Craft::info('Event Handlers installed',__METHOD__);


    }

    /**
     * Generates the autocomplete variable
     */
    public function generateAutocompleteVariable()
    {
        Craft::info('Autocomplete variable generated',__METHOD__);
    }
}
