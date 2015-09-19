<?php

$app['factory.messages'] = $app->share(function () {
    return new \FP\Larmo\Infrastructure\Factory\MessageCollection;
});

$app['provider.authinfo'] = $app->share(function ($app) {
    return new \FP\Larmo\Infrastructure\Adapter\PhpArrayAuthInfoProvider($app['config.authinfo']);
});

$app['service.plugins'] = $app->share(function ($app) {
    $pluginsCollection = new \FP\Larmo\Domain\Service\PluginsCollection;
    $directoryIterator = new \DirectoryIterator($app['config.path.plugins']);
    $pluginsRepository = new \FP\Larmo\Infrastructure\Repository\FilesystemPlugins($app, $directoryIterator);
    $pluginsRepository->retrieve($pluginsCollection);

    $pluginService = new \FP\Larmo\Application\PluginService($pluginsCollection);

    foreach ($pluginService->getPluginSubscribers() as $subscriber) {
        $app['dispatcher']->addSubscriber($subscriber);
    }

    return $pluginService;
});

$app['service.filters'] = $app->share(function () {
    return new \FP\Larmo\Domain\Service\FiltersCollection;
});

$app['service.json_schema_validation'] = function () {
    return new \FP\Larmo\Application\Adapter\VendorJsonSchemaValidation;
};

$app['service.packet_validation'] = function ($app) {
    $validator = $app['service.json_schema_validation'];
    $authinfo = $app['provider.authinfo'];
    $availableSources = $app['config.sources'];

    return new \FP\Larmo\Application\PacketValidationService($validator, $authinfo, $availableSources);
};
