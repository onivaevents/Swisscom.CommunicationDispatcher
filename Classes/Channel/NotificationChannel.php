<?php

namespace Swisscom\CommunicationDispatcher\Channel;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Party\Domain\Model\Person;
use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Swisscom\CommunicationDispatcher\Domain\Model\Notification;
use Swisscom\CommunicationDispatcher\Domain\Repository\NotificationRepository;
use Swisscom\CommunicationDispatcher\Exception;

class NotificationChannel implements ChannelInterface
{

    /**
     * @Flow\Inject
     * @var NotificationRepository
     */
    protected $notificationRepository;

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @param array $options
     * @throws Exception
     */
    public function send(Recipient $recipient, string $subject, string $text, array $options = [])
    {
        if ($recipient->getPerson() instanceof Person) {
            $newNotification = $this->createNotification($recipient, $subject, $text);
            $this->notificationRepository->add($newNotification);
        } else {
            throw new Exception('Notification expects the recipient to have a Person.', 1513086601);
        }
    }

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @return Notification
     */
    protected function createNotification(Recipient $recipient, string $subject, string $text)
    {
         return new Notification($recipient->getPerson(), $subject, $text);
    }
}
