<?php

use craft\helpers\FileHelper;
use nystudio107\autocomplete\Autocomplete;
use nystudio107\autocomplete\handlers\GenerateHandler;

afterEach(function () {
    FileHelper::clearDirectory(Craft::$app->getPath()->getCompiledClassesPath());
});

test('can get default generators', function () {
    $handler = new GenerateHandler();
    $classes = $handler->getAllGenerators();
    expect($classes)->toBe(Autocomplete::DEFAULT_AUTOCOMPLETE_GENERATORS);
});

test('generator class is added via event', function () {
    registerGeneratorViaEvent('RandomClassName');
    $handler = new GenerateHandler();
    $classes = $handler->getAllGenerators();
    expect($classes)->toContain('RandomClassName');
});

test('throws exception if generator class does not exist', function () {
    registerGeneratorViaEvent('UnknownClassNameXXX');
    $handler = new GenerateHandler();
    $handler->handle();
})->throws(InvalidArgumentException::class, "Unable to resolve 'UnknownClassNameXXX'");

test('throws exception if class does not implement interface', function () {
    registerGeneratorViaEvent('IncompatibleGenerator');
    $handler = new GenerateHandler();
    $handler->handle();
})->throws(InvalidArgumentException::class, "Class 'IncompatibleGenerator' is no instance of GeneratorInterface");


// Helper function
// Resets the $types to the defaults + given class name
function registerGeneratorViaEvent(string $class) {
    \yii\base\Event::on(Autocomplete::class,
        Autocomplete::EVENT_REGISTER_AUTOCOMPLETE_GENERATORS,
        function(\craft\events\RegisterComponentTypesEvent $event) use($class) {
            $event->types = array_merge(Autocomplete::DEFAULT_AUTOCOMPLETE_GENERATORS, [$class]);
        }
    );
}

// Dummy class we try to resolve from the container
class IncompatibleGenerator {}
