<?php

$config = [
    //'controllerNamespace' => 'commands',
    'controllerMap' => [
        'presql' => '\luya\commands\PresqlController',
        'exec' => '\luya\commands\ExecutableController',
        'crud' => '\luya\commands\CrudController',
    ],
    'components' => [
        'collection' => 'luya\components\Collection',
        'luya' => 'luya\components\LuyaComponents',
    ],
    'bootstrap' => [
        'luya\components\BootstrapCli',
    ],
];

return $config;
