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

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.0
 */
interface GeneratorInterface
{
    // Constants
    // =========================================================================

    public const TEMPLATE_EXTENSION = '.php';
    public const STUBS_EXTENSION = '.php.stub';
    public const STUBS_DIR = DIRECTORY_SEPARATOR . 'stubs';

    // Public Static Methods
    // =========================================================================

    /**
     * Return the name of the generator
     *
     * @return string
     */
    public static function getGeneratorName(): string;

    /**
     * Return a file system path to the generator stubs directory
     *
     * @return string
     */
    public static function getGeneratorStubsPath(): string;

    /**
     * Generate the autocomplete class if it doesn't exist already
     */
    public static function generate();

    /**
     * Regenerate the autocomplete classes from scratch
     */
    public static function regenerate();

    /**
     * Delete the autocomplete classes
     */
    public static function delete();
}
