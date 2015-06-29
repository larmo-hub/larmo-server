<?php

use FP\Larmo\Infrastructure\Service\MessageCollectionConversion;

$app->get('/latestMessages', function (Request $request) use ($app) {
    $jsonContent = $request->getContent();

    $filters = $app['filters.service'](json_decode($jsonContent, true));
    $messages = $app['messages.repository']->retrieve($filters);
    $collectionConverter = new MessageCollectionConversion($messages);

    return $app->json($collectionConverter->getCollectionAsArray());
});