<?php
/**
 * Autocomplete plugin for Craft CMS 3.x
 *
 * Provides Twig template IDE autocomplete of Craft CMS & plugin variables
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2021 nystudio107
 */

namespace nystudio107\autocomplete\base;

use nystudio107\autocomplete\Autocomplete;

use Craft;

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.0
 */
abstract class Generator implements GeneratorInterface
{
    // Traits
    // =========================================================================

    use GeneratorTrait;

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
    public static function generate(): void
    {
    }

    /**
     * @inheritDoc
     */
    public static function regenerate(): void
    {
    }

    // Protected Static Methods
    // =========================================================================

    /**
     * Save the template to disk, with variable substituation
     *
     * @param array $vars key/value variables to be replaced in the stub
     * @return bool Whether the template was successefully saved
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
     * Don't regenerate the file if it already exists, and is less than 10 seconds old
     *
     * @return bool
     */
    protected static function shouldRegenerateFile(): bool
    {
        $path = self::getGeneratedFilePath();
        if (is_file($path)) {
            if (time() - filemtime($path) > 10) {
                return false;
            }
        }

        return true;
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
        return Autocomplete::getInstance()->basePath . self::STUBS_DIR . static::getGeneratorName() . self::STUBS_EXTENSION;
    }

}
