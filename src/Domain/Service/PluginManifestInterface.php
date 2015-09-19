<?php

namespace FP\Larmo\Domain\Service;

interface PluginManifestInterface
{
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return string
     */
    public function getDisplayName();

    /**
     * @return array
     */
    public function getEventSubscriber();
}