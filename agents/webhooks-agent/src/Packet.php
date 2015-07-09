<?php

namespace FP\Larmo\Agents\WebHookAgent;

use FP\Larmo\Agents\WebHookAgent\Exceptions\InvalidConfigurationException;
use FP\Larmo\Agents\WebHookAgent\Services\ServiceDataInterface;

class Packet
{
    private $packetArray;

    public function __construct($metadataObject, ServiceDataInterface $service)
    {
        $metadata = $metadataObject->getMetadata();
        $messages = $service->getData();
        $this->packetArray = $this->preparePacket($metadata, $messages);
    }

    private function preparePacket($metadata, $messages)
    {
        return array(
            'metadata' => $metadata,
            'data' => $messages
        );
    }

    public function getPacket()
    {
        return $this->packetArray;
    }

    public function send($uri)
    {
        $packetJson = json_encode($this->packetArray);

        if(!$uri) {
            trigger_error("No target URIs specified in configuration. Sending will NOT proceed.", E_USER_WARNING);
            throw new InvalidConfigurationException;
        }

        if (!is_array($uri)) {
            $targets = [$uri];
        } else {
            $targets = $uri;
        }

        $header = [
            'accept: */*',
            'Content-Type: application/json',
            'Accept-Charset: utf-8',
        ];

        $errors = [];
        $results = [];

        foreach ($targets as $targetUri) {
            try {
                $curl = curl_init($targetUri);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $packetJson);
                $curl_response = curl_exec($curl);
                curl_close($curl);
                $results[$targetUri] = $curl_response;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors) {
            foreach ($errors as $uri => $error) {
                trigger_error('Errors in transmission for ' . $uri . ': ' . $error, E_USER_WARNING);
            }
        }

        if ($results) {
            foreach ($results as $uri => $result) {
                $response = json_decode($result);
                if ($response) {
                    trigger_error('Response from ' . $uri . ': ' . json_encode($response->message), E_USER_NOTICE);
                }
            }
        }
    }
}
