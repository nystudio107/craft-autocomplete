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

namespace nystudio107\autocomplete;

use nystudio107\autocomplete\base\Generator;
use nystudio107\autocomplete\console\controllers\AutocompleteController;
use nystudio107\autocomplete\generators\AutocompleteTwigExtensionGenerator;
use nystudio107\autocomplete\generators\AutocompleteVariableGenerator;

use Craft;
use craft\console\Application as CraftConsoleApp;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Plugins;
use craft\web\Application as CraftWebApp;

use nystudio107\autocomplete\handlers\GenerateAutocompleteTemplates;
use nystudio107\autocomplete\handlers\RegenerateAutocompleteTemplates;
use yii\base\Application as YiiApp;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Module;

/**
 * Class Autocomplete
 *
 * @author    nystudio107
 * @package   Autocomplete
 * @since     1.0.0
 */
class Autocomplete extends Module implements BootstrapInterface
{
    // Constants
    // =========================================================================

    /**
     * @event RegisterComponentTypesEvent The event that is triggered when registering
     *        Autocomplete Generator types
     *
     * Autocomplete Generator types must implement [[GeneratorInterface]]. [[Generator]]
     * provides a base implementation.
     *
     * ```php
     * use nystudio107\autocomplete\Autocomplete;
     * use craft\events\RegisterComponentTypesEvent;
     * use yii\base\Event;
     *
     * Event::on(Autocomplete::class,
     *     Autocomplete::EVENT_REGISTER_AUTOCOMPLETE_GENERATORS,
     *     function(RegisterComponentTypesEvent $event) {
     *         $event->types[] = MyAutocompleteGenerator::class;
     *     }
     * );
     * ```
     */
    const EVENT_REGISTER_AUTOCOMPLETE_GENERATORS = 'registerAutocompleteGenerators';

    const DEFAULT_AUTOCOMPLETE_GENERATORS = [
        AutocompleteVariableGenerator::class,
        AutocompleteTwigExtensionGenerator::class,
    ];

    // Public Methods
    // =========================================================================

    /**
     * Bootstraps the extension
     *
     * @param YiiApp|CraftWebApp|CraftConsoleApp $app
     */
    public function bootstrap($app)
    {
        // Set the currently requested instance of this module class,
        // so we can later access it with `Autocomplete::getInstance()`
        static::setInstance($this);

        // Make sure it's Craft
        if (!($app instanceof CraftWebApp || $app instanceof CraftConsoleApp)) {
            return;
        }
        // Make sure we're in devMode
        if (!$app->config->general->devMode) {
            return;
        }

        // Register our event handlers
        $this->registerEventHandlers();

        // Add our console controller
        if ($app->request->isConsoleRequest) {
            $app->controllerMap['autocomplete'] = AutocompleteController::class;
        }

    }

    /**
     * Registers our event handlers
     */
    public function registerEventHandlers()
    {
        Event::on(Plugins::class,Plugins::EVENT_AFTER_INSTALL_PLUGIN, new RegenerateAutocompleteTemplates());
        Event::on(Plugins::class,Plugins::EVENT_AFTER_UNINSTALL_PLUGIN, new RegenerateAutocompleteTemplates());
        Event::on(Plugins::class,Plugins::EVENT_AFTER_LOAD_PLUGINS, new GenerateAutocompleteTemplates());
        Craft::info('Event Handlers installed',__METHOD__);
    }

}
