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

test('types for properties', function ($value, $output) {
    $globals = ['key_does_not_matter' => $value];
    $formatter = new TwigVariableFormatter([], $globals);
    $result = $formatter->globalTwigVariables();

    expect($result['key_does_not_matter'])->toBe($output);

})->with([
    [true, 'true'],
    [123, 123],
    [new stdClass(), 'new \stdClass()'],
    [[1 => 2], '[]'],
    [null, 'null']
]);



