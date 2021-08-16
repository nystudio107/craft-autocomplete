<?php

namespace nystudio107\autocomplete\handlers;

use Craft;

class RegenerateHandler extends GenerateHandler
{

    public function __invoke(\yii\base\Event $event): void
    {
        $this->handle();
        $event->handled = true;
    }

    public function handle(): void
    {
        $autocompleteGenerators = $this->getAllGenerators();

        foreach($autocompleteGenerators as $class) {
            $generator = $this->resolve($class);
            $generator->beforeGenerate();
            $generator->regenerate();
        }
        Craft::info('Autocomplete templates re-generated',__METHOD__);
    }
}
