<?php

use FP\Larmo\Application\Event\RetrieveMessagesEvent;
use FP\Larmo\Domain\Service\LarmoEvents;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->get('/latestMessages', function (Request $request) use ($app) {
    $filters = $app['service.filters'];

    try {
        $filters->addFilters($request->query->all());
    } catch (\InvalidArgumentException $e) {
        return $app->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    }

    $messages = $app['factory.messages']->createEmptyCollection();

    $retrieveMsgEvent = new RetrieveMessagesEvent();
    $retrieveMsgEvent->setMessages($messages);
    $retrieveMsgEvent->setFilters($filters);

    $app['dispatcher']->dispatch(LarmoEvents::RETRIEVE_MESSAGES, $retrieveMsgEvent);

    if ($retrieveMsgEvent->hasErrors()) {
        return $app->json(['message' => $retrieveMsgEvent->getErrors()], Response::HTTP_BAD_REQUEST);
    }

    $outputMessages = [];

    /* @todo use convertMessageCollectionToArray instead of this loop? */
    foreach ($retrieveMsgEvent->getMessages() as $message) {
        $singleMessage = [
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

        array_push($outputMessages, $singleMessage);
    }

    return $app->json($outputMessages, Response::HTTP_OK);
})
    ->before(function (Request $request, Application $app) {

        // make sure there is a plugin that will be able to handle this request
        if (!$app['dispatcher']->hasListeners(LarmoEvents::RETRIEVE_MESSAGES)) {
            return $app->json(['message' => 'Hub does not support data retrieval yet.'], Response::HTTP_BAD_REQUEST);
        }
    });