<?php

$router = $di->getRouter();

$router->add(
    '/api/:controller/:action',
    [
        'controller' => 1,
        'action'     => 2,
        'mobile'     => TRUE,
    ]
);

$router->add(
    '/api/:controller/:action/:params',
    [
        'controller' => 1,
        'action'     => 2,
        'params'     => 3,
        'mobile'     => TRUE,
    ]
);

$router->handle();
