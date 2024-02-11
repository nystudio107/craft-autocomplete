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

namespace nystudio107\autocomplete\base;

use Craft;
use nystudio107\autocomplete\Autocomplete;

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.0
 */
abstract class Generator implements GeneratorInterface
{
    // Constants
    // =========================================================================

    /**
     * @event Event The event that is triggered before generating autocomplete classes.
     *
     * ```php
     * use nystudio107\autocomplete\events\DefineGeneratorValuesEvent;
     * use nystudio107\autocomplete\generators\AutocompleteTwigExtensionGenerator;
     * use yii\base\Event;
     *
     * Event::on(AutocompleteTwigExtensionGenerator::class,
     *     AutocompleteTwigExtensionGenerator::EVENT_BEFORE_GENERATE,
     *     function(DefineGeneratorValuesEvent $event) {
     *         $event->values['myVariable'] = 'value';
     *     }
     * );
     * ```
     */
    public const EVENT_BEFORE_GENERATE = 'beforeGenerate';

    // Public Static Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public static function getGeneratorName(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public static function getGeneratorStubsPath(): string
    {
        return Autocomplete::getInstance()->basePath . self::STUBS_DIR;
    }

    /**
     * @inheritDoc
     */
    public static function generate()
    {
    }

    /**
     * @inheritDoc
     */
    public static function regenerate()
    {
    }

    /**
     * @inheritDoc
     */
    public static function delete()
    {
        $path = static::getGeneratedFilePath();
        if (is_file($path)) {
            @unlink($path);
        }
    }

    // Protected Static Methods
    // =========================================================================

    /**
     * Save the template to disk, with variable substitution
     *
     * @param array $vars key/value variables to be replaced in the stub
     * @return bool Whether the template was successfully saved
     */
    protected static function saveTemplate(array $vars): bool
    {
        $stub = file_get_contents(static::getStubFilePath());
        if ($stub) {
            $template = str_replace(array_keys($vars), array_values($vars), $stub);

            return !(file_put_contents(static::getGeneratedFilePath(), $template) === false);
        }

        return false;
    }

    /**
     * Don't regenerate the file if it already exists
     *
     * @return bool
     */
    protected static function shouldRegenerateFile(): bool
    {
        return !is_file(static::getGeneratedFilePath());
    }

    /**
     * Return a path to the generated autocomplete template file
     *
     * @return string
     */
    protected static function getGeneratedFilePath(): string
    {
        return Craft::$app->getPath()->getCompiledClassesPath() . DIRECTORY_SEPARATOR . static::getGeneratorName() . self::TEMPLATE_EXTENSION;
    }

    /**
     * Return a path to the autocomplete template stub
     *
     * @return string
     */
    protected static function getStubFilePath(): string
    {
        return static::getGeneratorStubsPath() . DIRECTORY_SEPARATOR . static::getGeneratorName() . self::STUBS_EXTENSION;
    }
}
