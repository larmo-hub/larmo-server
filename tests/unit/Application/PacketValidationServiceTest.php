<?php

use FP\Larmo\Application\Adapter\VendorJsonSchemaValidation;
use FP\Larmo\Application\PacketValidationService;
use FP\Larmo\Infrastructure\Adapter\PhpArrayAuthInfoProvider;

class PacketValidationServiceTest extends PHPUnit_Framework_TestCase
{
    private $packetValidation;
    private $schema;

    protected function setup()
    {
        $jsonValidator = new VendorJsonSchemaValidation();
        $authinfo = new PhpArrayAuthInfoProvider(['agent' => 'key']);
        $availableSources = [['id' => 'test']];

        $this->schema = __DIR__ . '/../../../config/packet.scheme.json';
        $this->packetValidation = new PacketValidationService($jsonValidator, $authinfo, $availableSources);
    }

    protected function getPacketMock()
    {
        $authinfo = new stdClass();
        $authinfo->agent = 'agent';
        $authinfo->auth = 'key';

        $metadata = new stdClass();
        $metadata->timestamp = time();
        $metadata->source = 'test';
        $metadata->authinfo = $authinfo;

        $packet = new stdClass();
        $packet->metadata = $metadata;
        $packet->data = [];

        return $packet;
    }

    /**
     * @test
     */
    public function setSchemaWorks()
    {
        $this->assertInstanceOf(PacketValidationService::class,
            $this->packetValidation->setSchemaFromFile($this->schema));
    }

    /**
     * @test
     */
    public function setPacketWorks()
    {
        $this->assertInstanceOf(PacketValidationService::class, $this->packetValidation->setPacket([]));
    }

    /**
     * @test
     */
    public function validatePacketShouldWorks()
    {
        $packet = $this->getPacketMock();

        $this->packetValidation->setSchemaFromFile($this->schema);
        $this->packetValidation->setPacket($packet);
        $this->packetValidation->isValid();

        $this->assertTrue($this->packetValidation->isValid());
    }

    /**
     * @test
     */
    public function errorsAreArray()
    {
        $this->assertTrue(is_array($this->packetValidation->getErrors()));
    }
}
