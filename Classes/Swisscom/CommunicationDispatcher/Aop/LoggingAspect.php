<?php
namespace Swisscom\CommunicationDispatcher\Aop;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
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
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Neos\Flow\Log\ThrowableStorageInterface
     * @Flow\Inject
     */
    protected $throwableStorage;

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

        $context = ['recipient' => $recipient];
        if (!empty($subject)) {
            $context['subject'] = $subject;
        }

        if ($joinPoint->hasException()) {
            $context['exception'] = $this->throwableStorage->logThrowable($joinPoint->getException());
            $this->logger->error($className . ': Dispatching failed', $context);
        } else {
            $this->logger->info($className . ': Dispatching successful', $context);
        }
    }
}
