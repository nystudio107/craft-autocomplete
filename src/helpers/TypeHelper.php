<?php
/**
 * Autocomplete plugin for Craft CMS 3.x
 *
 * Provides Twig template IDE autocomplete of Craft CMS & plugin variables
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2021 nystudio107
 */

namespace nystudio107\autocomplete\helpers;

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.0
 */
abstract class TypeHelper
{
    // Public Static Methods
    // =========================================================================

    /**
     * Return the type of the passed in variable
     *
     * @param $value
     *
     * @return string
     */
    public static function getType($value): string
    {
        switch ($value) {
            case \is_bool($value):
                return 'bool';
            case \is_int($value):
                return 'int';
            case \is_string($value):
                return 'string';
            case \is_object($value):
                return \get_class($value);
            default:
                return '';
        }
    }
}
