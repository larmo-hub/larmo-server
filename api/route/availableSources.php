<?php

$app->get('/availableSources', function () use ($app) {
    return $app->json($app['config.sources']);
});