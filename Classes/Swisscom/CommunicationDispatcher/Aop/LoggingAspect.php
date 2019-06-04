<?php
namespace Swisscom\CommunicationDispatcher\Aop;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Swisscom\CommunicationDispatcher\Log\LoggerInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class LoggingAspect
{

    /**
     * @Flow\Inject
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Logs dispatcher calls
     * @Flow\After("within(Swisscom\CommunicationDispatcher\Channel\ChannelInterface) && method(.*->send())")
     * @param JoinPointInterface $joinPoint The current joinpoint
     * @return void
     */
    public function logDispatch(JoinPointInterface $joinPoint)
    {
        /** @var Recipient $recipient */
        $recipient = $joinPoint->getMethodArgument('recipient');
        $subject = $joinPoint->getMethodArgument('subject');
        $className = $joinPoint->getClassName();

        if (empty($subject)) {
            $message = $className . ': Dispatching message to ' . $recipient->getEmail();
        } else {
            $message = $className . ': Dispatching message "' . $subject . '" to ' . $recipient->getEmail();
        }

        if ($joinPoint->hasException()) {
            $this->logger->log($message . ' failed', LOG_ERR);
        } else {
            $this->logger->log($message . ' successful', LOG_INFO);
        }
    }
}
