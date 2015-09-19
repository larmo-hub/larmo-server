<?php

use FP\Larmo\Domain\Service\PluginsCollection;
use FP\Larmo\Infrastructure\Repository\FilesystemPlugins as FilesystemPluginsRepository;

class FilesystemPluginRepositoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function pluginsDirectoryExists()
    {
        $path = __DIR__ . '/../../../plugins';
        $path = realpath($path);

        $this->assertNotEmpty($path, 'Please fix path to plugins directory');

        return $path;
    }

    /**
     * @test
     * @depends pluginsDirectoryExists
     * @param $path
     */
    public function filesystemPluginsRepositoryWillLoadPlugins($path)
    {
        $path = __DIR__ . '/../../../plugins';
        $path = realpath($path);

        $app = $this->getMockBuilder('\Silex\Application')->disableOriginalConstructor()->getMock();
        $directoryIterator = new \DirectoryIterator($path);
        $adapter = new FilesystemPluginsRepository($app, $directoryIterator);
        $collection = new PluginsCollection();

        $this->assertEquals(0, count($collection));

        $adapter->retrieve($collection);

        $this->assertTrue(count($collection) > 0);
    }
}