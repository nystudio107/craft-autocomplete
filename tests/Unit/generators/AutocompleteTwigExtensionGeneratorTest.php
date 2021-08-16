<?php

use craft\helpers\FileHelper;
use nystudio107\autocomplete\generators\AutocompleteTwigExtensionGenerator;

afterEach(function () {
    FileHelper::clearDirectory(Craft::$app->getPath()->getCompiledClassesPath());
});


test('can generate some file ', function () {
    $generator = new AutocompleteTwigExtensionGenerator(Craft::$app->getView());
    $generator->beforeGenerate();
    $success = $generator->generate();

    expect($success)->toBeTrue();
});

test('do not generate if file exist', function () {
    $generator = new AutocompleteTwigExtensionGenerator(Craft::$app->getView());
    $generator->beforeGenerate();
    $success1 = $generator->generate();
    $success2 = $generator->generate();

    expect($success1)->toBeTrue();
    expect($success2)->toBeFalse();
});



