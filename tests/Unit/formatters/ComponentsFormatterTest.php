<?php

use craft\web\twig\variables\Request;
use craft\web\twig\variables\Config;
use craft\web\twig\variables\UserSession;


use nystudio107\autocomplete\generators\formatter\ComponentsFormatter;

test('common globals are mapped to class names', function () {
    $variable = new \craft\web\twig\variables\CraftVariable();
    $formatter = new ComponentsFormatter($variable);
    $result = $formatter->getPreparedComponents();

    expect($result['config'])->toBe(Config::class);
    expect($result['request'])->toBe(Request::class);
    expect($result['session'])->toBe(UserSession::class);
});
