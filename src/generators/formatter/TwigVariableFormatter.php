<?php

namespace nystudio107\autocomplete\generators\formatter;

use craft\base\Element;

class TwigVariableFormatter
{

    private array $elementTypes;

    private array $globals;


    public function __construct(array $elementTypes = [], array $globals = [])
    {
        $this->elementTypes = $elementTypes;
        $this->globals = $globals;
    }

    public function globalTwigVariables() : array
    {
        $globals = [];

        // Iterate through the globals in the Twig context
        foreach ($this->globals as $key => $value) {
            $type = gettype($value);
            switch ($type) {
                case 'object':
                    $globals[$key] = 'new \\' . get_class($value) . '()';
                    break;

                case 'boolean':
                    $globals[$key] = $value ? 'true' : 'false';
                    break;

                case 'integer':
                case 'double':
                    $globals[$key] = $value;
                    break;

                case 'string':
                    $globals[$key] = "'" . addslashes($value) . "'";
                    break;

                case 'array':
                    $globals[$key] = '[]';
                    break;

                case 'NULL':
                    $globals[$key] = 'null';
                    break;
            }
        }

        return $globals;
    }

    /**
     * Add in the element types that could be injected as route variables
     *
     * @return array
     */
    public function elementRouteVariables(): array
    {
        $routeVariables = [];
        foreach ($this->elementTypes as $elementType) {
            /* @var Element $elementType */
            $key = $elementType::refHandle();
            $routeVariables[$key] = 'new \\' . $elementType . '()';
        }

        return $routeVariables;
    }
}
