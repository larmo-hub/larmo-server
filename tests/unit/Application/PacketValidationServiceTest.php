<?php

use FP\Larmo\Application\Adapter\VendorJsonSchemaValidation;
use FP\Larmo\Application\PacketValidationService;
use FP\Larmo\Infrastructure\Adapter\PhpArrayAuthInfoProvider;
use FP\Larmo\Infrastructure\Repository\FilesystemPlugins as FilesystemPluginsRepository;
use FP\Larmo\Domain\Service\PluginsCollection;
use FP\Larmo\Application\PluginService;
use FP\Larmo\Domain\Aggregate\Packet;
use FP\Larmo\Domain\Entity\Metadata;
use FP\Larmo\Domain\Service\MessageCollection;

class PacketValidationServiceTest extends PHPUnit_Framework_TestCase
{
    private $packetValidation;
    private $schema;

    public function setup()
    {
        $path = __DIR__ . '/../../../';
        $this->schema = __DIR__ . '/../../../config/packet.scheme.json';

        $jsonValidator = new VendorJsonSchemaValidation();
        $authinfo = new PhpArrayAuthInfoProvider(['webhooks' => 'NJS78350hi[2-0,capos0q2u5noafuig']);

        $directoryIterator = new \DirectoryIterator($path . 'src/Plugin');
        $pluginRepository = new FilesystemPluginsRepository($directoryIterator);
        $pluginCollection = new PluginsCollection;
        $pluginRepository->retrieve($pluginCollection);
        $pluginsService = new PluginService($pluginCollection);

        $this->packetValidation = new PacketValidationService($jsonValidator, $authinfo, $pluginsService);
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
        $authInfoValidator = $this->getMockBuilder('\FP\Larmo\Domain\Service\AuthInfoInterface')->setMethods(array('validate'))->getMock();
        $metadata = new Metadata($authInfoValidator, time(), 'AUTH_INFO', 'SOURCE');
        $messages = new MessageCollection();
        $packet = new Packet($messages, $metadata);

        $this->assertInstanceOf(PacketValidationService::class, $this->packetValidation->setPacket($packet));
    }

    public function validatePacketShouldWorks()
    {
        $this->assertTrue($this->packetValidation->isValid());
    }

    public function errorsAreArray()
    {
        $this->assertTrue(is_array($this->packetValidation->getErrors()));
    }
}
