<?php

namespace FP\Larmo\Agents\WebHookAgent\Services;

abstract class ServiceAbstract implements ServiceDataInterface {
    protected $data;
    protected $serviceName;
    protected $eventHeader;
    protected $eventType;

    protected function getEventType($requestHeaders)
    {
        $key = $this->eventHeader;
        if(is_array($requestHeaders) && array_key_exists($key, $requestHeaders)) {
            return $requestHeaders[$key];
        }

        return null;
    }

    protected function prepareData($data)
    {
        if ($this->eventType) {
            $eventClass = $this->getEventClass();

            if (class_exists($eventClass)) {
                $event = new $eventClass($data);
                return $event->getMessages();
            }
        }

        throw new \InvalidArgumentException;
    }

    protected function getEventClass()
    {
        $eventTypeArray = explode('_', $this->eventType);
        $eventClassPath = '\\FP\\Larmo\\Agents\\WebHookAgent\\Services\\' . ucfirst($this->serviceName) . '\\Events\\';
        return $eventClassPath . implode('', array_map('ucfirst', $eventTypeArray));
    }

    public function getData()
    {
        return $this->data;
    }

    public function getServiceName()
    {
        return $this->serviceName;
    }
}
