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
    public const STUBS_EXTENSION = '.stub.php';
    public const STUBS_DIR = DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;

    // Public Static Methods
    // =========================================================================

    /**
     * Return the name of the generator
     *
     * @return string
     */
    public static function getGeneratorName(): string;

    /**
     * Generate the auto-complete file if it doesn't exist already
     */
    public static function generate(): void;

    /**
     * Regenerate the auto-complete from scratch
     */
    public static function regenerate(): void;
}
