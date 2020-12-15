<?php

namespace Swisscom\CommunicationDispatcher\Domain\Repository;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryInterface;
use Neos\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class NotificationRepository extends Repository
{

    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'timestamp' => QueryInterface::ORDER_DESCENDING
    );
}
