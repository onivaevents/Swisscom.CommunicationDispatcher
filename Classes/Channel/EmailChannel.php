<?php

namespace Swisscom\CommunicationDispatcher\Channel;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\Exception as ResourceException;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\SymfonyMailer\Exception\InvalidMailerConfigurationException;
use Neos\SymfonyMailer\Service\MailerService;
use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Swisscom\CommunicationDispatcher\Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * @Flow\Scope("prototype")
 */
class EmailChannel implements ChannelInterface
{
    /**
     * @Flow\Inject
     * @var MailerService
     */
    protected $mailerService;

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var string|null
     */
    protected $transport;

    /**
     * @var array<string, string>
     */
    protected $from;

    /**
     * @var array<string, string>
     */
    protected $replyTo;

    /**
     * @var array<string, string>
     */
    protected $cc;

    /**
     * @param array $options
     */
    function __construct(array $options = [])
    {
        $this->transport = $options['transport'] ?? null;
        $this->from = $options['from'] ?? '';
        $this->replyTo = $options['replyTo'] ?? '';
        $this->cc = $options['cc'] ?? '';
    }

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @param array $options
     * @return void
     * @throws Exception
     * @throws ResourceException
     * @throws InvalidMailerConfigurationException
     * @throws TransportExceptionInterface
     */
    public function send(Recipient $recipient, string $subject, string $text, array $options = [])
    {
        $toEmail = $recipient->getEmail();
        $toName = $recipient->getName();
        $attachedResources = isset($options['attachedResources']) && is_array($options['attachedResources'])
            ? $options['attachedResources']
            : [];

        if (empty($toEmail)) {
            throw new Exception('Recipient has no email address', 1570541186);
        }

        $email = new Email();
        $email
            ->from($this->arrayToAddress($this->from))
            ->subject($subject);

        if (!empty($this->replyTo)) {
            $email->replyTo($this->arrayToAddress($this->replyTo));
        }
        $email->to(new Address($toEmail, $toName));
        if (!empty($this->cc)) {
            $email->cc($this->arrayToAddress($this->cc));
        }
        $email->subject(htmlspecialchars_decode($subject));

        $html = $this->formatInternetMessage($text);
        $text = $this->formatInternetMessage(strip_tags($text));

        $email->html($html);
        $email->text($text);

        foreach ($attachedResources as $name => $resource) {
            if ($resource instanceof PersistentResource) {
                if ($path = $this->getPathFromPersistentResource($resource)) {
                    $email->attachFromPath($path, $resource->getFilename(), $resource->getMediaType());
                }
            } elseif (is_string($resource) && is_string($name)) {
                $email->attach($resource,  $name, 'text/plain');
            }
        }

        $transport = null;
        if ($this->transport !== null) {
            $transport = Transport::fromDsn($this->transport);
        }

        $mailer = $this->mailerService->getMailer($transport);
        $mailer->send($email);
    }

    /**
     * Format according to https://datatracker.ietf.org/doc/html/rfc5322#section-2.1.1
     */
    private function formatInternetMessage(string $text): string
    {
        // Break the text into lines with a maximum of 78 characters
        $text = wordwrap($text, 78, PHP_EOL, true);

        // Ensure no line or continuous sequence exceeds 998 characters
        $lines = explode(PHP_EOL, $text);
        foreach ($lines as &$line) {
            if (strlen($line) > 998) {
                $line = chunk_split($line, 998, PHP_EOL);
            }
        }
        return implode(PHP_EOL, $lines);
    }

    public function getPathFromPersistentResource(PersistentResource $resource): ?string
    {
        if (!is_string($resource->getSha1())) {
            // Throw exception to prevent type error on getCacheEntryIdentifier(): "Return value must be of type string, null returned"
            throw new ResourceException('No sha1 set in persistent resource', 1733826832);
        }

        // No exception handling here. This provides flexibility to handle it outside or by aspects
        return $resource->createTemporaryLocalCopy();
    }

    /**
     * @param array<string, string> $array
     */
    protected function arrayToAddress(array $array): Address
    {
        $email = (string)array_key_first($array);

        return new Address($email, $array[$email]);
    }
}
