<?php
namespace Swisscom\CommunicationDispatcher\Domain\Model;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Party\Domain\Model\Person;

/**
 * @Flow\Entity
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
     */
    protected $text = '';

    /**
     * @var boolean
     */
    protected $isRead = false;

    /**
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * Notification constructor.
     * @param Person $person
     * @param string $subject
     * @param string $text
     */
    public function __construct(Person $person, $subject, $text)
    {
        $this->timestamp = new \DateTime();
        $this->subject = $subject;
        $this->text = $text;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function isIsRead()
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

}
