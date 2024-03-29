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

namespace nystudio107\autocomplete\events;

use yii\base\Event;

/**
 * @author    nystudio107
 * @package   autocomplete
 * @since     1.0.4
 */
class DefineGeneratorValuesEvent extends Event
{
    /**
     * @var array Key-value pairs of values that will be used by the generator.
     */
    public $values = [];
}
