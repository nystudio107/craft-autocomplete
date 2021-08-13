<?php

namespace nystudio107\autocomplete\handlers;

use Craft;
use craft\events\RegisterComponentTypesEvent;
use nystudio107\autocomplete\Autocomplete;
use nystudio107\autocomplete\base\GeneratorInterface;
use yii\base\Event;

class GenerateAutocompleteTemplates
{

    public function __invoke(\yii\base\Event $event): void
    {
        $autocompleteGenerators = $this->getAllGenerators();

        foreach($autocompleteGenerators as $class) {
            $generator = $this->resolve($class);
            $generator->beforeGenerate();
            $generator->generate();
        }
        Craft::info('Autocomplete templates generated',__METHOD__);
    }

    /**
     * Returns all available autocomplete generator classes.
     *
     * @return string[] The available autocomplete generator classes
     */
    public function getAllGenerators(): array
    {
        $event = new RegisterComponentTypesEvent([
            'types' => Autocomplete::DEFAULT_AUTOCOMPLETE_GENERATORS
        ]);

        Event::trigger(
            Autocomplete::class,
            Autocomplete::EVENT_REGISTER_AUTOCOMPLETE_GENERATORS,
            $event
        );

        return array_unique($event->types, SORT_REGULAR);
    }

    protected function resolve(string $class): GeneratorInterface
    {
        /* @var \nystudio107\autocomplete\base\GeneratorInterface $generator */
        $generator = Craft::$container->get($class);

        if (!($generator instanceof GeneratorInterface)) {
            throw new \InvalidArgumentException("$class is no instance of GeneratorInterface");
        }

        return $generator;
    }

}
