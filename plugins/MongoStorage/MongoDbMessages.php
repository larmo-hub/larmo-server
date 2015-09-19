<?php

namespace FP\Larmo\Plugin\MongoStorage;

;

use FP\Larmo\Domain\ValueObject\UniqueId;
use FP\Larmo\Domain\Service\FiltersCollection;
use FP\Larmo\Domain\Service\MessageCollection;
use FP\Larmo\Domain\Repository\Messages as MessagesRepository;

use FP\Larmo\Infrastructure\Factory\Message as MessageFactory;

final class MongoDbMessages implements MessagesRepository
{
    /**
     * @var MongoDbStorage
     */
    private $storage;
    private $collectionName = 'messages';
    private $defaultSortBy = 'timestamp';
    private $lastErrorMsg;

    /**
     * @param MongoDbStorage $storage
     */
    public function __construct(MongoDbStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param MessageCollection $messages
     * @return mixed
     */
    public function store(MessageCollection $messages)
    {
        $response = $this->storage->batchInsert($this->collectionName, $this->convertMessageCollectionToArray($messages));
        if (is_array($response) && !empty($response['err'])) {
            $this->lastErrorMsg = $response['err'];
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param MessageCollection $messages
     * @param FiltersCollection $filters
     *
     * @return bool
     */
    public function retrieve(MessageCollection $messages, FiltersCollection $filters = null)
    {

        $findQuery = $this->prepareQuery($filters->getFilter('data'));

        try {
            $retrieved = $this->storage->find($this->collectionName, $findQuery);

            if ($filters->hasFilter('limit')) {
                $retrieved->limit($filters->getFilter('limit'));
            }

            if ($filters->hasFilter('offset')) {
                $retrieved->skip($filters->getFilter('offset'));
            }

            $retrieved->sort(array($this->defaultSortBy => -1));
        } catch (\MongoException $e) {
            $this->lastErrorMsg = $e->getMessage();
            return false;
        }

        foreach ($retrieved as $message) {
            $uniqueId = new UniqueId($message['id']);
            $messageFactory = new MessageFactory($uniqueId);
            $messages[] = $messageFactory->fromArray($message);
        }

        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareQuery($data)
    {
        $query = array();
        foreach ($data as $field => $value) {
            if (is_array($value)) {
                $query[$field] = array('$in' => $value);
            } else {
                $query[$value] = $value;
            }
        }

        return $query;
    }

    /**
     * @param MessageCollection $messages
     * @return array
     */
    private function convertMessageCollectionToArray(MessageCollection $messages)
    {
        $outputArray = [];

        foreach ($messages as $message) {
            $messageArray = [
                'id' => $message->getMessageId(),
                'source' => explode('.', $message->getType())[0],
                'type' => $message->getType(),
                'timestamp' => $message->getTimestamp(),
                'author' => [
                    'name' => $message->getAuthor()->getFullName(),
                    'login' => $message->getAuthor()->getNickName(),
                    'email' => $message->getAuthor()->getEmail()
                ],
                'body' => $message->getBody(),
                'extras' => $message->getExtras()
            ];

            array_push($outputArray, $messageArray);
        }

        return $outputArray;
    }

    /**
     * @return string|null
     */
    public function getLastErrorMsg()
    {
        return $this->lastErrorMsg;
    }
}
