<?php

namespace Swisscom\CommunicationDispatcher\Domain\Model;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Party\Domain\Model\Person;

/**
 * @Flow\Entity
 * @ORM\InheritanceType("JOINED")
 */
class Notification
{

    /**
     * @var Person
     * @ORM\ManyToOne
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $person;

    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $text = '';

    /**
     * @var boolean
     */
    protected $notified = false;

    /**
     * @var DateTime
     */
    protected $timestamp;

    /**
     * Notification constructor.
     * @param Person $person
     * @param string $subject
     * @param string $text
     */
    public function __construct(Person $person, string $subject, string $text)
    {
        $this->timestamp = new DateTime();
        $this->person = $person;
        $this->subject = $subject;
        $this->text = $text;
    }

    /**
     * @return Person
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function isNotified(): bool
    {
        return $this->notified;
    }

    /**
     * @param bool $notified
     */
    public function setNotified(bool $notified)
    {
        $this->notified = $notified;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

}
