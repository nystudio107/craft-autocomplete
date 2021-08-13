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
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGeneratorName(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getGeneratorStubsPath(): string
    {
        return realpath(__DIR__ . '/..' . self::STUBS_DIR);
    }

    /**
     * @inheritDoc
     */
    public function beforeGenerate()
    {
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
    }

    /**
     * @inheritDoc
     */
    public function regenerate()
    {
    }

    // Protected Static Methods
    // =========================================================================

    /**
     * Save the template to disk, with variable substitution
     *
     * @param array $vars key/value variables to be replaced in the stub
     * @return bool Whether the template was successfully saved
     */
    protected function saveTemplate(array $vars): bool
    {
        $stub = file_get_contents($this->getStubFilePath());
        if ($stub) {
            $template = str_replace(array_keys($vars), array_values($vars), $stub);

            return !(file_put_contents($this->getGeneratedFilePath(), $template) === false);
        }

        return false;
    }

    /**
     * Don't regenerate the file if it already exists
     *
     * @return bool
     */
    protected function shouldRegenerateFile(): bool
    {
        return !is_file($this->getGeneratedFilePath());
    }

    /**
     * Return a path to the generated autocomplete template file
     *
     * @return string
     */
    protected function getGeneratedFilePath(): string
    {
        return Craft::$app->getPath()->getCompiledClassesPath() . DIRECTORY_SEPARATOR . static::getGeneratorName() . self::TEMPLATE_EXTENSION;
    }

    /**
     * Return a path to the autocomplete template stub
     *
     * @return string
     */
    protected function getStubFilePath(): string
    {
        return static::getGeneratorStubsPath() . DIRECTORY_SEPARATOR . static::getGeneratorName() . self::STUBS_EXTENSION;
    }
}
