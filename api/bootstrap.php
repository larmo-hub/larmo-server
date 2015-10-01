<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Provider\Silex\WhoopsServiceProvider;

if ($app['debug']) {
    $app->register(new WhoopsServiceProvider);
}

$app['service.plugins']->initPlugins();

foreach ($app['service.plugins']->getPluginSubscribers() as $subscriber) {
    $app['dispatcher']->addSubscriber($subscriber);
}

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
});