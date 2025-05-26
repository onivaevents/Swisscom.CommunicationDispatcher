<?php

namespace Swisscom\CommunicationDispatcher\Channel;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\Exception as ResourceException;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\SwiftMailer\Message;
use Swift_Attachment;
use Swift_Image;
use Swift_Message;
use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Swisscom\CommunicationDispatcher\Exception;

/**
 * @Flow\Scope("prototype")
 */
class EmailChannel implements ChannelInterface
{

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $replyTo;

    /**
     * @var string
     */
    protected $cc;

    /**
     * @param array $options
     */
    function __construct(array $options = [])
    {
        $this->from = isset($options['from']) ? $options['from'] : '';
        $this->replyTo = isset($options['replyTo']) ? $options['replyTo'] : '';
        $this->cc = isset($options['cc']) ? $options['cc'] : '';
    }

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @param array $options
     * @return void
     * @throws Exception
     */
    public function send(Recipient $recipient, string $subject, string $text, array $options = [])
    {
        $toEmail = $recipient->getEmail();
        $toName = $recipient->getName();
        $attachedResources = $options['attachedResources'] ?? [];

        if (empty($toEmail)) {
            throw new Exception('Recipient has no email address', 1570541186);
        }
        $mail = new Message();
        $mail->setFrom($this->from);
        if (!empty($this->replyTo)) {
            $mail->setReplyTo($this->replyTo);
        }
        $mail->setTo($toEmail, $toName);
        if (!empty($this->cc)) {
            $mail->setCc($this->cc);
        }
        $mail->setSubject(htmlspecialchars_decode($subject));
        $plaintext = preg_replace(array('/\s{2,}/', '/[\t]/', '/###IMAGE:(.+?)###/', '/###PLAIN:(.+?)###/'), ' ', strip_tags($text));
        $text = $this->embedResources($text, $mail);

        $text = $this->formatInternetMessage($text);
        $plaintext = $this->formatInternetMessage($plaintext);

        $mail->setBody($text, 'text/html', 'utf-8');
        $mail->addPart($plaintext, 'text/plain', 'utf-8');
        foreach ($attachedResources as $resource) {
            if ($resource instanceof PersistentResource) {
                if ($swiftAttachment = $this->createSwiftAttachmentFromPersistentResource($resource)) {
                    $mail->attach($swiftAttachment);
                }
            } elseif ($resource instanceof Swift_Attachment) {
                $mail->attach($resource);
            }
        }

        $acceptedRecipients = $mail->send();
        if ($acceptedRecipients <= 0) {
            throw new Exception('Sending SwiftMessage failed', 1570541189);
        }
    }

    /**
     * Embed images. I.e:
     * <img height="40px" src="###IMAGE:'{template.logo}'###" alt="Logo"/>
     *
     * @param string $html
     * @param Swift_Message $mail
     * @return string $html
     */
    private function embedResources(string $html, Swift_Message &$mail): string
    {
        $html = preg_replace_callback('/###IMAGE:(.+?)###/', function ($matches) use ($mail) {
            return $this->embedImageResourceCallback($matches, $mail);
        }, $html);
        $html = preg_replace_callback('/###PLAIN:(.+?)###/', function ($matches) use ($mail) {
            return $this->embedPlainResourceCallback($matches);
        }, $html);

        return $html;
    }

    /**
     * @param array $matches
     * @param \Swift_Message $mail
     * @return string
     */
    private function embedImageResourceCallback(array $matches, Swift_Message &$mail): string
    {
        $cid = '';
        if (isset($matches[1])) {
            $source = trim($matches[1], '\'');
            try {
                $cid = $mail->embed(Swift_Image::fromPath($source));
            } catch (\Exception $e) {
                // Nothing to do here
            }
        }
        return $cid;
    }

    /**
     * @param array $matches
     * @return string
     */
    private function embedPlainResourceCallback(array $matches): string
    {
        $plain = '';
        if (isset($matches[1])) {
            $source = trim($matches[1], '\'');
            try {
                $plain = file_get_contents($source);
            } catch (\Exception $e) {
                // Nothing to do here
            }
        }
        return $plain;
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

    public function createSwiftAttachmentFromPersistentResource(PersistentResource $resource): ?Swift_Attachment
    {
        if (!is_string($resource->getSha1())) {
            // Throw exception to prevent type error on getCacheEntryIdentifier(): "Return value must be of type string, null returned"
            throw new ResourceException('No sha1 set in persistent resource', 1733826832);
        }

        // No exception handling here. This provides flexibility to handle it outside or by aspects
        $path = $resource->createTemporaryLocalCopy();
        $attachment = Swift_Attachment::fromPath($path, $resource->getMediaType());
        $attachment->setFilename($resource->getFilename());

        return $attachment;
    }
}
