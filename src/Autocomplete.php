<?php
/**
 * Autocomplete module for Craft CMS
 *
 * Provides Twig template IDE autocomplete of Craft CMS & plugin variables
 *
 * @link      https://nystudio107.com
 * @link      https://putyourlightson.com
 * @copyright Copyright (c) nystudio107
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace nystudio107\autocomplete;

use Craft;
use craft\console\Application as CraftConsoleApp;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Globals;
use craft\services\Plugins;
use craft\web\Application as CraftWebApp;
use nystudio107\autocomplete\base\Generator;
use nystudio107\autocomplete\console\controllers\AutocompleteController;
use nystudio107\autocomplete\generators\AutocompleteTwigExtensionGenerator;
use nystudio107\autocomplete\generators\AutocompleteVariableGenerator;
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

    public const ID = 'craft-autocomplete';

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
    public const EVENT_REGISTER_AUTOCOMPLETE_GENERATORS = 'registerAutocompleteGenerators';

    public const DEFAULT_AUTOCOMPLETE_GENERATORS = [
        AutocompleteVariableGenerator::class,
        AutocompleteTwigExtensionGenerator::class,
    ];

    // Private Properties
    // =========================================================================

    private $allAutocompleteGenerators;

    // Public Methods
    // =========================================================================

    /**
     * @inerhitdoc
     */
    public function __construct($id = self::ID, $parent = null, $config = [])
    {
        /**
         * Explicitly set the $id parameter, as earlier versions of Yii2 look for a
         * default parameter, and depend on $id being explicitly set:
         * https://github.com/yiisoft/yii2/blob/f3d1534125c9c3dfe8fa65c28a4be5baa822e721/framework/di/Container.php#L436-L448
         */
        parent::__construct($id, $parent, $config);
    }

    /**
     * Bootstraps the extension
     *
     * @param YiiApp $app
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
        if (!Craft::$app->config->general->devMode) {
            return;
        }

        // Register our event handlers
        $this->registerEventHandlers();

        // Add our console controller
        if (Craft::$app->request->isConsoleRequest) {
            Craft::$app->controllerMap['autocomplete'] = AutocompleteController::class;
        }
    }

    /**
     * Registers our event handlers
     */
    public function registerEventHandlers()
    {
        Event::on(Plugins::class, Plugins::EVENT_AFTER_INSTALL_PLUGIN, [$this, 'regenerateAutocompleteClasses']);
        Event::on(Plugins::class, Plugins::EVENT_AFTER_UNINSTALL_PLUGIN, [$this, 'deleteAutocompleteClasses']);
        Event::on(Globals::class, Globals::EVENT_AFTER_SAVE_GLOBAL_SET, [$this, 'deleteAutocompleteClasses']);
        Event::on(CraftWebApp::class, CraftWebApp::EVENT_INIT, [$this, 'generateAutocompleteClasses']);
        Craft::info('Event Handlers installed', __METHOD__);
    }

    /**
     * Call each of the autocomplete generator classes to tell them to generate their classes if they don't exist already
     */
    public function generateAutocompleteClasses()
    {
        $autocompleteGenerators = $this->getAllAutocompleteGenerators();
        foreach ($autocompleteGenerators as $generatorClass) {
            /* @var Generator $generatorClass */
            $generatorClass::generate();
        }
        Craft::info('Autocomplete classes generated', __METHOD__);
    }

    /**
     * Call each of the autocomplete generator classes to tell them to regenerate their classes from scratch
     */
    public function regenerateAutocompleteClasses()
    {
        $autocompleteGenerators = $this->getAllAutocompleteGenerators();
        foreach ($autocompleteGenerators as $generatorClass) {
            /* @var Generator $generatorClass */
            $generatorClass::regenerate();
        }
        Craft::info('Autocomplete classes regenerated', __METHOD__);
    }

    /**
     * Call each of the autocomplete generator classes to tell them to delete their classes
     */
    public function deleteAutocompleteClasses()
    {
        $autocompleteGenerators = $this->getAllAutocompleteGenerators();
        foreach ($autocompleteGenerators as $generatorClass) {
            /* @var Generator $generatorClass */
            $generatorClass::delete();
        }
        Craft::info('Autocomplete classes deleted', __METHOD__);
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
        if ($this->allAutocompleteGenerators) {
            return $this->allAutocompleteGenerators;
        }

        $event = new RegisterComponentTypesEvent([
            'types' => self::DEFAULT_AUTOCOMPLETE_GENERATORS,
        ]);
        $this->trigger(self::EVENT_REGISTER_AUTOCOMPLETE_GENERATORS, $event);
        $this->allAutocompleteGenerators = $event->types;

        return $this->allAutocompleteGenerators;
    }
}
