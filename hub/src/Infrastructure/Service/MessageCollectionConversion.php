<?php

namespace FP\Larmo\Infrastructure\Service;

use FP\Larmo\Domain\Service\MessageCollection;

class MessageCollectionConversion {
    private $collection;

    public function __construct(MessageCollection $collection)
    {
        $this->collection = $collection;
        $this->collectionArray = $this->convertMessageCollectionToArray();
    }

    private function convertMessageCollectionToArray()
    {
        $collection = [];

        foreach($this->collection as $message) {
            $messageArray = [
                'messageId' => $message->getMessageId(),
                'source' => explode('.', $message->getType())[0],
                'type' => $message->getType(),
                'timestamp' => $message->getTimestamp(),
                'author' => [
                    'fullName' => $message->getAuthor()->getFullName(),
                    'nickName' => $message->getAuthor()->getNickName(),
                    'email' => $message->getAuthor()->getEmail()
                ],
                'body' => $message->getBody(),
                'extras' => $message->getExtras()
            ];

            $collection[] = $messageArray;
        }

        return $collection;
    }

    public function getCollectionAsArray()
    {
        return $this->collectionArray;
    }
}
