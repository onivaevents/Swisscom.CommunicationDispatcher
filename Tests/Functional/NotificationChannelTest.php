<?php

namespace Swisscom\CommunicationDispatcher\Tests\Functional;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Channel\NotificationChannel;
use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use TYPO3\Flow\Persistence\Doctrine\PersistenceManager;
use TYPO3\Flow\Tests\FunctionalTestCase;
use TYPO3\Party\Domain\Model\Person;
use TYPO3\Party\Domain\Model\PersonName;

/**
 * Class NotificationChannelTest
 * @package Swisscom\CommunicationNotificationChannel\Tests\Functional
 */
class NotificationChannelTest extends FunctionalTestCase
{

    /**
     * @var NotificationChannel
     */
    protected $notificationChannel;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->notificationChannel = $this->objectManager->get(NotificationChannel::class);
        $this->persistenceManager = $this->objectManager->get(PersistenceManager::class);
    }

    /**
     * @test
     */
    public function dispatcherRendersText()
    {
        $personName = new PersonName('Foo', 'Bar');
        $person = new Person();
        $person->setName($personName);
        $recipient = new Recipient($person);
        $this->notificationChannel->send($recipient, 'Subject', 'Text');

        $this->assertEquals(true, $this->persistenceManager->hasUnpersistedChanges());
    }
}
