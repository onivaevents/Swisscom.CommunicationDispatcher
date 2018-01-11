<?php
namespace Swisscom\CommunicationDispatcher\Aop;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Swisscom\CommunicationDispatcher\Log\LoggerInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Aop\JoinPointInterface;

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
     * @Flow\After("within(Swisscom\CommunicationDispatcher\Dispatcher\DispatcherInterface) && method(.*->dispatch())")
     * @param JoinPointInterface $joinPoint The current joinpoint
     * @return void
     */
    public function logDispatch(JoinPointInterface $joinPoint)
    {
        /** @var Recipient $recipient */
        $recipient = $joinPoint->getMethodArgument('recipient');
        $subject = $joinPoint->getMethodArgument('subject');

        if ($joinPoint->hasException()) {
            $this->logger->log('Dispatching message "' . $subject . '" to ' . $recipient->getName() . ' failed', LOG_ERR);
        } else {
            $this->logger->log('Dispatching message "' . $subject . '" to ' . $recipient->getName() . ' successful', LOG_INFO);
        }
    }
}
