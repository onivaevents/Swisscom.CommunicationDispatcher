<?php

namespace Swisscom\CommunicationDispatcher\Channel;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Neos\Flow\Annotations as Flow;

/**
 * Channel interface
 */
interface ChannelInterface
{

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @param array $options
     * @return void
     */
    public function send(Recipient $recipient, $subject, $text, $options = array());
}
