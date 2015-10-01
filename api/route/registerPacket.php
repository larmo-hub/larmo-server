<?php

use Silex\Application;
use FP\Larmo\Domain\Service\LarmoEvents;
use FP\Larmo\Application\Event\IncomingMessageEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->post('/registerPacket', function (Request $request) use ($app) {
    $packetDataAsObject = json_decode($request->getContent());
    $packetDataAsArray = json_decode($request->getContent(), true);

    $packetValidation = $app['service.packet_validation']->setSchemaFromFile($app['config.path.packet_scheme'])
        ->setPacket($packetDataAsObject);

    if ($packetValidation->isValid()) {
        // transform messages from packet to domain collection
        $messages = $app['factory.messages']->fromArray($packetDataAsArray['data']);

        $incomingMsgEvent = new IncomingMessageEvent();
        $incomingMsgEvent->setMessages($messages);

        $app['dispatcher']->dispatch(LarmoEvents::INCOMING_MESSAGE, $incomingMsgEvent);

        if ($incomingMsgEvent->hasErrors()) {
            return $app->json(['message' => $incomingMsgEvent->getErrors()], Response::HTTP_BAD_REQUEST);
        } else {
            return $app->json(['message' => 'OK'], Response::HTTP_OK);
        }
    } else {
        return $app->json(['message' => $packetValidation->getErrors()], Response::HTTP_BAD_REQUEST);
    }
})->before(function (Request $request, Application $app) {

    // make sure there is a plugin that will be able to handle this request
    if (!$app['dispatcher']->hasListeners(LarmoEvents::INCOMING_MESSAGE)) {
        return $app->json(['message' => 'Hub is not yet configured to handle incoming messages.'],
            Response::HTTP_BAD_REQUEST);
    }
});