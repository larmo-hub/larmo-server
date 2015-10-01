<?php

namespace FP\Larmo\Plugin\MongoStorage;

use FP\Larmo\Domain\Service\PluginManifestInterface;

final class PluginManifest implements PluginManifestInterface
{
    private $ident = 'mongodb';
    private $name = 'MongoDB Storage';

    /**
     * @var MongoDbStorage|false
     */
    private $storage;

    /**
     * @var MongoDbMessages
     */
    private $repository;

    /**
     * @todo lazy loading for storage / repository
     */
    public function __construct()
    {
        try {
            $this->storage = new MongoDbStorage(
                getenv('MONGO_DB_URL'),
                getenv('MONGO_DB_PORT'),
                getenv('MONGO_DB_USER'),
                getenv('MONGO_DB_PASSWORD'),
                getenv('MONGO_DB_NAME'),
                []
            );

            $this->repository = new MongoDbMessages($this->storage);
        } catch (\RuntimeException $exception) {
            $this->repository = false;
        }
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->ident;
    }

    /**
     * If system cannot connect to MongoDB, don't register
     * it's capability to handle messages.
     *
     * @return EventSubscriber
     */
    public function getEventSubscriber()
    {
        if ($this->repository) {
            return new EventSubscriber($this->repository);
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name;
    }
}