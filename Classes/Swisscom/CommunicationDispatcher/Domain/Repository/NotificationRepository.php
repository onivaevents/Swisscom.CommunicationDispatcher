<?php
namespace Swisscom\CommunicationDispatcher\Domain\Repository;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class NotificationRepository extends Repository
{

    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'timestamp' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING
    );
}
