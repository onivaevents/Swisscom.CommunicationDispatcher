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
     * @param string $dispatcherObjectName
     * @param string $channelObjectName
     * @param array $channelOptions
     * @return DispatcherInterface
     */
    public function create($dispatcherObjectName, $channelObjectName, array $channelOptions = [])
    {
        /** @var DispatcherInterface $dispatcher */
        $dispatcher = new $dispatcherObjectName();
        /** @var ChannelInterface $channel */
        $channel = new $channelObjectName($channelOptions);
        $dispatcher->setChannelInterface($channel);

        return $dispatcher;
    }
}
