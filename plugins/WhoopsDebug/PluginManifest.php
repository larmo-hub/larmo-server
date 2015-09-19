<?php

namespace FP\Larmo\Plugin\WhoopsDebug;

use Whoops\Provider\Silex\WhoopsServiceProvider;
use FP\Larmo\Domain\Service\PluginManifest as PluginManifestAbstract;

class PluginManifest extends PluginManifestAbstract
{
    private $ident = 'whoopsdebug';
    private $name = 'WhoopsDebug';

    public function __construct($app)
    {
        parent::__construct($app);

        if ($this->app['debug']) {
            $this->app->register(new WhoopsServiceProvider);
        }
    }

    public function getIdentifier()
    {
        return $this->ident;
    }

    public function getEventSubscriber()
    {
        return null;
    }

    public function getDisplayName()
    {
        return $this->name;
    }
}