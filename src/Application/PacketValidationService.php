<?php

namespace FP\Larmo\Application;

use FP\Larmo\Application\Contract\JsonSchemaValidation;
use FP\Larmo\Domain\Service\AuthInfoInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

final class PacketValidationService
{
    private $schema;
    private $packet;
    private $validator;
    private $authinfo;
    private $availableSources;
    private $errors = [];

    public function __construct(
        JsonSchemaValidation $schemaValidation,
        AuthInfoInterface $authinfo,
        array $availableSources
    ) {
        $this->validator = $schemaValidation;
        $this->authinfo = $authinfo;
        $this->availableSources = $availableSources;
    }

    public function setSchemaFromFile($schemaFilePath)
    {
        if (!file_exists($schemaFilePath) || !is_readable($schemaFilePath)) {
            throw new Exception('Can\'t read the specified file: ' . $schemaFilePath);
        }

        $this->schema = json_decode(file_get_contents($schemaFilePath));

        return $this;
    }

    public function setPacket($packet)
    {
        $this->packet = $packet;

        return $this;
    }

    public function isValid()
    {
        $this->errors = [];
        $this->validator->check($this->packet, $this->schema);

        if (!$this->packet) {
            $this->errors[] = [
                'property' => '',
                'message' => 'the packet is empty or has invalid JSON structure'
            ];

            return false;
        }

        if (!$this->validator->isValid()) {
            $this->errors = $this->validator->getErrors();

            return false;
        }

        if (!$this->authinfo->validate(get_object_vars($this->packet->metadata->authinfo))) {
            $this->errors[] = [
                'property' => 'metadata.authinfo',
                'message' => 'the authinfo is invalid'
            ];

            return false;
        }

        if (array_search($this->packet->metadata->source, array_column($this->availableSources, 'id')) === false) {
            $this->errors[] = [
                'property' => 'metadata.source',
                'message' => 'the source is invalid'
            ];

            return false;
        }

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}