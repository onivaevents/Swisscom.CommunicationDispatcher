<?php

namespace Swisscom\CommunicationDispatcher\Domain\Model\Dto;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

/**
 * A DTO for storing send result information
 */
class SendResult implements \JsonSerializable
{
    public function __construct(
        public string $messageId,
    ) {}

    public function toArray(): array
    {
        return [
            'messageId' => $this->messageId,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
