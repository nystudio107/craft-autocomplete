<?php

namespace nystudio107\autocomplete\generators\formatter;

use craft\web\twig\variables\CraftVariable;

class ComponentsFormatter
{
    protected CraftVariable $variable;

    public function __construct(CraftVariable $variable)
    {
        $this->variable = $variable;
    }

    public function getPreparedComponents(): array
    {
        $properties = [];

        foreach ($this->variable->getComponents() as $key => $value) {
            $type = gettype($value);
            switch ($type) {
                case 'object':
                    $className    = get_class($value);
                    $properties[$key] = $className;
                    break;

                case 'array':
                    if (isset($value['class'])) {
                        $properties[$key] = $value['class'];
                    }
                    break;

                case 'string':
                    $properties[$key] = $value;
                    break;
            }
        }

        return $properties;

    }
}
