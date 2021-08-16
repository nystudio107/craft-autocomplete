<?php

use craft\helpers\FileHelper;
use craft\web\twig\variables\CraftVariable;
use nystudio107\autocomplete\generators\AutocompleteVariableGenerator;

use function Spatie\Snapshots\assertMatchesSnapshot;

afterEach(function () {
    FileHelper::clearDirectory(Craft::$app->getPath()->getCompiledClassesPath());
});

test('can access template stub', function () {
    $generator = new AutocompleteVariableGenerator(Craft::$app->getView());
    $path = $generator->getGeneratorStubsPath();
    $files = array_map(fn($f) => basename($f), glob($path . DIRECTORY_SEPARATOR . '*'));

    expect($path)->toBeReadableDirectory();
    expect($files)->toContain('AutocompleteVariable.php.stub');
});

test('can generate some file ', function () {
    $generator = new AutocompleteVariableGenerator(Craft::$app->getView());
    $generator->beforeGenerate();
    $success = $generator->generate();

    expect($success)->toBeTrue();
});

test('do not generate if file exist', function () {
    $generator = new AutocompleteVariableGenerator(Craft::$app->getView());
    $generator->beforeGenerate();
    $success1 = $generator->generate();
    $success2 = $generator->generate();

    expect($success1)->toBeTrue();
    expect($success2)->toBeFalse();
});

test('beforeGenerate() fills craftVariable', function () {
    $generator = new AutocompleteVariableGenerator(Craft::$app->getView());
    $varBeforeCall = $generator->craftVariable;
    $generator->beforeGenerate();
    $varAfterCall = $generator->craftVariable;

    expect($varBeforeCall)->toBeNull();
    expect($varAfterCall)->toBeInstanceOf(CraftVariable::class);
});

test('template output with default data', function () {
    $generator = new AutocompleteVariableGenerator(Craft::$app->getView());
    $generator->beforeGenerate();
    $generator->generate();

    $contents = file_get_contents($generator->getGeneratedFilePath());

    assertMatchesSnapshot($contents);
});

