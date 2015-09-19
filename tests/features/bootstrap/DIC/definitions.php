<?php

$container = new \Pimple;

// Configuration
$container['config.path.plugins'] = __DIR__ . '/../../../../src/Plugin';
$container['config.path.authinfo'] = __DIR__ . '/../Fixtures/authinfo.ini';

// Definitions
$container['plugins'] = $container->share(function ($container) {
    $app = new \Silex\Application;
    $pluginsCollection = new \FP\Larmo\Domain\Service\PluginsCollection;
    $directoryIterator = new \DirectoryIterator($container['config.path.plugins']);
    $pluginsRepository = new \FP\Larmo\Infrastructure\Repository\FilesystemPlugins($app, $directoryIterator);
    $pluginsRepository->retrieve($pluginsCollection);

    return new \FP\Larmo\Application\PluginService($pluginsCollection);
});

$container['authinfo'] = $container->share(function ($container) {
    return new \FP\Larmo\Infrastructure\Adapter\IniFileAuthInfoProvider($container['config.path.authinfo']);
});

$container['json_schema_validation'] = function () {
    return new \FP\Larmo\Application\Adapter\VendorJsonSchemaValidation;
};

$container['packet_validation.service'] = function ($container) {
    $validator = $container['json_schema_validation'];
    $authinfo = $container['authinfo'];
    $plugins = $container['plugins'];

    return new \FP\Larmo\Application\PacketValidationService($validator, $authinfo, $plugins);
};

$container['metadata.entity'] = $container->protect(function ($metadata, $authinfo) {
    return new FP\Larmo\Domain\Entity\Metadata($authinfo, $metadata['timestamp'], $metadata['authinfo'], $metadata['source']);
});

$container['message_collection.service'] = $container->protect(function ($data) {
    $uniqueIDGenerator = new FP\Larmo\Infrastructure\Adapter\PhpUniqidGenerator;
    $uniqueIDValueObject = new FP\Larmo\Domain\ValueObject\UniqueId($uniqueIDGenerator);
    $messages = new FP\Larmo\Domain\Service\MessageCollection;

    foreach($data as $singleMessage) {
        $author = new FP\Larmo\Domain\ValueObject\Author('', '', $singleMessage['author']['email']);
        $messages->append(new FP\Larmo\Domain\Entity\Message(
                $singleMessage['type'],
                $singleMessage['timestamp'],
                $author,
                $uniqueIDValueObject,
                $singleMessage['message'])
        );
    }

    return $messages;
});

$container['packet.aggregate'] = $container->protect(function ($message, $metadata) {
    new FP\Larmo\Domain\Aggregate\Packet($message, $metadata);
});

return $container;