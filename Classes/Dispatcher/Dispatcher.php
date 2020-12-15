<?php

namespace Swisscom\CommunicationDispatcher\Dispatcher;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Neos\Flow\Annotations as Flow;
use Swisscom\CommunicationDispatcher\Channel\ChannelInterface;
use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Swisscom\CommunicationDispatcher\Service\MessageService;

class Dispatcher implements DispatcherInterface
{

    /**
     * @var ChannelInterface
     */
    protected $channelInterface;

    /**
     * @var MessageService
     * @Flow\Inject
     */
    protected $messageService;

    /**
     * @param ChannelInterface $channelInterface
     * @return void
     */
    public function setChannelInterface(ChannelInterface $channelInterface)
    {
        $this->channelInterface = $channelInterface;
    }

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @param array $params
     * @param array $options
     * @return void
     */
    public function dispatch(Recipient $recipient, string $subject, string $text, array $params = [], array $options = [])
    {
        $renderedSubject = $this->messageService->renderSubject($subject, $params);
        $renderedText = $this->messageService->renderText($text, $params);

        $this->channelInterface->send($recipient, $renderedSubject, $renderedText, $options);
    }
}
