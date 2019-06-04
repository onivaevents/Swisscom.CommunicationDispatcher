<?php
namespace Swisscom\CommunicationDispatcher\Domain\Model\Dto;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Neos\Party\Domain\Model\ElectronicAddress;
use Neos\Party\Domain\Model\Person;

/**
 * A DTO for storing recipient information
 */
class Recipient
{

    /**
     * @var Person
     */
    protected $person;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $name;

    /**
     * Recipient constructor.
     * @param Person|null $person
     * @param string $email
     * @param string $name
     */
    public function __construct(Person $person = null, $email = '', $name = '')
    {
        if ($person instanceof Person) {
            $this->person = $person;
            if ($person->getPrimaryElectronicAddress() instanceof ElectronicAddress) {
                $this->email = $person->getPrimaryElectronicAddress()->getIdentifier();
            }
            $this->name = $person->getName()->getFullName();
        }
        if (!empty($email)) {
            $this->email = $email;
        }
        if (!empty($name)) {
            $this->name = $name;
        }
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

}
