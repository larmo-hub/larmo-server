<?php

namespace FP\Larmo\Application;

use FP\Larmo\Domain\Exception\PluginException;
use FP\Larmo\Domain\Service\PluginsCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PluginService
 *
 * IS_INPUT and IS_OUTPUT constants are used to have basic segregation of plugins.
 * It is very likely that these constants will be (re)moved in future.
 *
 * @package FP\Larmo\Application
 */
final class PluginService
{
    /**
     * @var \FP\Larmo\Domain\Service\PluginsCollection
     */
    private $plugins;
    private $pluginNames = array();
    private $subscribers = array();
    private $availablePlugins = array();

    public function __construct(PluginsCollection $collection)
    {
        $this->availablePlugins = $collection;
    }

    public function initPlugins() {
        foreach ($this->availablePlugins as $pluginName => $pluginClass) {
            $plugin = new $pluginClass;
            $ident = $plugin->getIdentifier();

            if ($this->checkPluginIsRegistered($ident)) {
                throw new PluginException(sprintf('Plugin "%s" is already registered!', $ident));
            }

            $this->plugins[] = $plugin;
            $this->pluginNames[$ident] = $plugin->getDisplayName();

            $subscriber = $plugin->getEventSubscriber();
            if ($subscriber instanceof EventSubscriberInterface) {
                $this->subscribers[] = $subscriber;
            }
        }
    }

    /**
     * Returns identifiers for registered plugins
     *
     * @return array
     */
    public function getRegisteredPlugins()
    {
        return array_keys($this->pluginNames);
    }

    /**
     * Checks whether the plugin is registered
     *
     * @param string $id
     * @return bool
     */
    public function checkPluginIsRegistered($id)
    {
        return array_key_exists($id, $this->pluginNames);
    }

    /**
     * Returns plugin's display name or null if not found
     *
     * @param string $id
     * @return string|null
     */
    public function getPluginDisplayName($id)
    {
        return $this->checkPluginIsRegistered($id)
            ? $this->pluginNames[$id] : null;
    }

    /**
     * Returns all event listeners from plugins.
     *
     * @return array
     */
    public function getPluginSubscribers()
    {
        return $this->subscribers;
    }
}