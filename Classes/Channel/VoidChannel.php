<?php

namespace Swisscom\CommunicationDispatcher\Channel;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Swisscom\CommunicationDispatcher\Exception;

class VoidChannel implements ChannelInterface
{

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @param array $options
     * @throws Exception
     */
    public function send(Recipient $recipient, string $subject, string $text, array $options = [])
    {
    }
}
