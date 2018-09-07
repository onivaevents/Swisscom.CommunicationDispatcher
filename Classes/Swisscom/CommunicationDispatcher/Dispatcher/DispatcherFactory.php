<?php
namespace Swisscom\CommunicationDispatcher\Dispatcher;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Channel\ChannelInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * The factory used to create communication channel instances.
 *
 * @Flow\Scope("singleton")
 */
class DispatcherFactory
{

    /**
     * @Flow\InjectConfiguration(path="dispatcherConfigurations")
     * @var array
     */
    protected $dispatcherConfigurations;

    /**
     * @param string $identifier
     * @param array $channelOptions
     * @return DispatcherInterface
     */
    public function create($identifier, array $channelOptions = [])
    {
        $configuration = $this->dispatcherConfigurations[$identifier];
        $objectName = $configuration['objectName'];
        $channelObjectName = $configuration['channelObjectName'];

        /** @var DispatcherInterface $dispatcher */
        $dispatcher = new $objectName();
        /** @var ChannelInterface $channel */
        $channel = new $channelObjectName($channelOptions);
        $dispatcher->setChannelInterface($channel);

        return $dispatcher;
    }
}
