<?php

namespace FP\Larmo\Plugin\MongoStorage;

use FP\Larmo\Domain\Service\PluginManifest as PluginManifestAbstract;

final class PluginManifest extends PluginManifestAbstract
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
     * @param $app \Silex\Application
     * @todo lazy loading for storage / repository
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $config = $app['config.mongo_db'];

        try {
            $this->storage = new MongoDbStorage(
                $config['db_url'],
                $config['db_port'],
                $config['db_user'],
                $config['db_password'],
                $config['db_name'],
                $config['db_options']
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