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

use nystudio107\autocomplete\base\Generator;
use nystudio107\autocomplete\console\controllers\AutocompleteController;
use nystudio107\autocomplete\generators\AutocompleteTwigExtensionGenerator;
use nystudio107\autocomplete\generators\AutocompleteVariableGenerator;

use Craft;
use craft\console\Application as CraftConsoleApp;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Plugins;
use craft\web\Application as CraftWebApp;

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
 *
 * @property-read string[] $allAutocompleteGenerators
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

        // Set the currently requested instance of this module class,
        // so we can later access it with `Autocomplete::getInstance()`
        static::setInstance($this);

        // Register our event handlers
        $this->registerEventHandlers();

        if (Craft::$app->request->isConsoleRequest) {
            Craft::$app->controllerMap['autocomplete'] = AutocompleteController::class;
        }
    }

    /**
     * Registers our event handlers
     */
    public function registerEventHandlers()
    {
        Event::on(Plugins::class,Plugins::EVENT_AFTER_INSTALL_PLUGIN, [$this, 'generateAutocompleteTemplates']);
        Event::on(Plugins::class,Plugins::EVENT_AFTER_UNINSTALL_PLUGIN, [$this, 'generateAutocompleteTemplates']);
        Event::on(Plugins::class,Plugins::EVENT_AFTER_LOAD_PLUGINS, [$this, 'generateAutocompleteTemplates']);
        Craft::info('Event Handlers installed',__METHOD__);
    }

    /**
     * Call each of the autocomplete generator classes to tell them to generate their templates
     */
    public function generateAutocompleteTemplates()
    {
        $autocompleteGenerators = $this->getAllAutocompleteGenerators();
        foreach($autocompleteGenerators as $generatorClass) {
            /** @var Generator $generatorClass */
            $generatorClass::generate();
        }
        Craft::info('Autocomplete templates generated',__METHOD__);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Returns all available autocomplete generator classes.
     *
     * @return string[] The available autocomplete generator classes
     */
    public function getAllAutocompleteGenerators(): array
    {
        $autocompleteGenerators = array_unique(array_merge(
            self::DEFAULT_AUTOCOMPLETE_GENERATORS
        ), SORT_REGULAR);

        $event = new RegisterComponentTypesEvent([
            'types' => $autocompleteGenerators
        ]);
        $this->trigger(self::EVENT_REGISTER_AUTOCOMPLETE_GENERATORS, $event);

        return $event->types;
    }

}
