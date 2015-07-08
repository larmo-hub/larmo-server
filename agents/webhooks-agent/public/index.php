<?php

require_once('../vendor/autoload.php');

use FP\Larmo\Agents\WebHookAgent\Packet;
use FP\Larmo\Agents\WebHookAgent\Request;
use FP\Larmo\Agents\WebHookAgent\Routing;
use FP\Larmo\Agents\WebHookAgent\Metadata;
use FP\Larmo\Agents\WebHookAgent\Services;
use FP\Larmo\Agents\WebHookAgent\Services\ServiceFactory;
use FP\Larmo\Agents\WebHookAgent\Exceptions\EventTypeNotFoundException;
use FP\Larmo\Agents\WebHookAgent\Exceptions\InvalidConfigurationException;
use FP\Larmo\Agents\WebHookAgent\Exceptions\InvalidIncomingDataException;
use FP\Larmo\Agents\WebHookAgent\Exceptions\MethodNotAllowedHttpException;
use FP\Larmo\Agents\WebHookAgent\Exceptions\ServiceNotFoundException;

require_once('../config/config.php');

header('Content-type: application/json; charset=utf-8');

$response['status'] = 'error';
$response['message'] = '';

try {
    $input = file_get_contents('php://input');
    $request = new Request($_SERVER, $input);

    if (!$request->isPostMethod()) {
        throw new MethodNotAllowedHttpException;
    }

    /* Retrieve data from HTTP request */
    $uri = $request->getUri();

    /* Create appropriate service */
    $routing = new Routing($uri);
    $service = ServiceFactory::create($routing->getSourceIdentifier(), $request);

    /* Create metadata (header for packet) */
    $metadata = new Metadata($service->getServiceName(), $config['authentication']);

    /* Create and send packet */
    $packet = new Packet($metadata, $service);
    $packet->send($config['hubURI']);

    $response['status'] = 'success';
    $response['message'] = 'Packet saved';

} catch (MethodNotAllowedHttpException $e) {
    $response['message'] = 'POST only allowed';
    http_response_code(405); // POST only allowed
} catch (InvalidIncomingDataException $e) {
    $response['message'] = 'Data incorrect';
    http_response_code(400); // Data are incorrect
} catch (EventTypeNotFoundException $e) {
    $response['message'] = 'Event type not found';
    http_response_code(400); // We got an event type we are not prepared to handle
} catch (ServiceNotFoundException $e) {
    $response['message'] = 'Service isn\'t recognized';
    http_response_code(404); // We do not have the ability to handle this service
} catch (InvalidArgumentException $e) {
    $response['message'] = 'Invalid argument';
    http_response_code(404);
} catch (InvalidConfigurationException $e) {
    $response['message'] = 'Configuration error';
    http_response_code(500);
} catch (Exception $e) {
    /* Unpredicted error */
    $response['message'] = 'Something wrong: ' . $e->getMessage();
    trigger_error($e->getMessage(), E_USER_WARNING);
} finally {
    file_put_contents('php://stderr', $response['message']);
    echo json_encode($response);
}
