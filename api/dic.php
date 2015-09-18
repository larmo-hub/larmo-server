<?php

// register plugins service and event subscribers from events
$app['plugins'] = $app->share(function ($app) {
    $pluginsCollection = new \FP\Larmo\Domain\Service\PluginsCollection;
    $directoryIterator = new \DirectoryIterator($app['config.path.plugins']);
    $pluginsRepository = new \FP\Larmo\Infrastructure\Repository\FilesystemPlugins($directoryIterator);
    $pluginsRepository->retrieve($pluginsCollection);

    $pluginService = new \FP\Larmo\Application\PluginService($pluginsCollection);

    foreach ($pluginService->getPluginSubscribers() as $subscriber) {
        $app['dispatcher']->addSubscriber($subscriber);
    }

    return $pluginService;
});

$app['messages.factory'] = $app->share(function () {
    return new \FP\Larmo\Infrastructure\Factory\MessageCollection;
});

$app['filters.service'] = $app->share(function () {
    return new \FP\Larmo\Domain\Service\FiltersCollection;
});

$app['provider.authinfo'] = $app->share(function ($app) {
    return new \FP\Larmo\Infrastructure\Adapter\PhpArrayAuthInfoProvider($app['config.authinfo']);
});

$app['json_schema_validation'] = function () {
    return new \FP\Larmo\Application\Adapter\VendorJsonSchemaValidation;
};

$app['packet_validation.service'] = function ($app) {
    $validator = $app['json_schema_validation'];
    $authinfo = $app['authinfo'];
    $plugins = $app['plugins'];

    return new \FP\Larmo\Application\PacketValidationService($validator, $authinfo, $plugins);
};
