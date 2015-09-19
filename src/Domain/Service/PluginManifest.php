<?php

namespace FP\Larmo\Domain\Service;

abstract class PluginManifest implements PluginManifestInterface
{
    /**
     * @param $app \Silex\Application
     */
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
}