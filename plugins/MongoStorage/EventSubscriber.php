<?php

namespace FP\Larmo\Plugin\MongoStorage;

use FP\Larmo\Application\Event\IncomingMessageEvent;
use FP\Larmo\Application\Event\RetrieveMessagesEvent;
use FP\Larmo\Domain\Service\LarmoEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{

    /**
     * @var MongoDbStorage
     */
    private $storage;

    public function __construct(MongoDbMessages $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param IncomingMessageEvent $event
     */
    public function onStoreMessages(IncomingMessageEvent $event)
    {
        if (false === $this->storage->store($event->getMessages())) {
            $event->setError('MongoDB Write failed: '. $this->storage->getLastErrorMsg());
        }
    }

    /**
     * @param RetrieveMessagesEvent $event
     */
    public function onRetrieveMessages(RetrieveMessagesEvent $event)
    {
        if (false === $this->storage->retrieve($event->getMessages(), $event->getFilters())) {
            $event->setError('MongoDB Error occurred while trying to retrieve data: '. $this->storage->getLastErrorMsg());
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            LarmoEvents::INCOMING_MESSAGE => array('onStoreMessages', 0),
            LarmoEvents::RETRIEVE_MESSAGES => array('onRetrieveMessages', 0),
        );
    }
}