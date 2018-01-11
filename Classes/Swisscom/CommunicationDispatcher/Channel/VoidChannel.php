<?php
namespace Swisscom\CommunicationDispatcher\Channel;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Swisscom\CommunicationDispatcher\Exception;
use TYPO3\Flow\Annotations as Flow;

/**
 * Class VoidChannel for testing purpose
 * @package Swisscom\CommunicationDispatcher\Channel
 */
class VoidChannel implements ChannelInterface
{

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @param array $attachedResources
     * @throws Exception
     */
    public function send(Recipient $recipient, $subject, $text, $attachedResources = array())
    {
    }
}
