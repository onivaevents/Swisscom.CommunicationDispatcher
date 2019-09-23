<?php

namespace Swisscom\CommunicationDispatcher\Dispatcher;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Channel\ChannelInterface;
use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Neos\Flow\Annotations as Flow;

class Dispatcher implements DispatcherInterface
{

    /**
     * @var ChannelInterface
     */
    protected $channelInterface;

    /**
     * @var \Swisscom\CommunicationDispatcher\Service\MessageService
     * @Flow\Inject
     */
    protected $messageService;

    /**
     * @param ChannelInterface $channelInterface
     * @return void
     */
    public function setChannelInterface(ChannelInterface $channelInterface = null)
    {
        $this->channelInterface = $channelInterface;
    }

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @param array $params
     * @param array $attachedResources
     * @return void
     */
    public function dispatch(Recipient $recipient, $subject, $text, $params = array(), $attachedResources = array())
    {
        $renderedSubject = $this->messageService->renderSubject($subject, $params);
        $renderedText = $this->messageService->renderText($text, $params);

        $this->channelInterface->send($recipient, $renderedSubject, $renderedText, $attachedResources);
    }
}
