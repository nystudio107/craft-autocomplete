<?php

use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use nystudio107\autocomplete\generators\formatter\TwigVariableFormatter;

test('element route variables are mapped to class names', function ($key, $class) {

    $formatter = new TwigVariableFormatter([$class], []);
    $result = $formatter->elementRouteVariables();

    expect($result[$key])->toBe('new \\' . $class . '()');

})->with([
    ['asset', Asset::class],
    ['category', Category::class],
    ['entry', Entry::class],
]);
