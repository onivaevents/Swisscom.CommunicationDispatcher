<?php
namespace Swisscom\CommunicationDispatcher\Channel;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use Swisscom\CommunicationDispatcher\Domain\Repository\AssetRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Resource\Resource;
use TYPO3\Flow\Resource\ResourceManager;
use TYPO3\Media\Domain\Model\Asset;

/**
 * @Flow\Scope("prototype")
 */
class EmailChannel implements ChannelInterface
{
    /**
     * @Flow\Inject
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @Flow\Inject
     * @var \TYPO3\SwiftMailer\MailerInterface
     */
    protected $mailer;

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
     * @param array $attachedResources
     * @return void
     * @throws \Exception
     */
    public function send(Recipient $recipient, $subject, $text, $attachedResources = array())
    {
        $toEmail = $recipient->getEmail();
        $toName = $recipient->getName();

        if (! empty($toEmail)) {
            $mail = new \Swift_Message();
            $mail->setFrom($this->from);
            $mail->setReplyTo($this->replyTo);
            $mail->setTo($toEmail, $toName);
            if (!empty($this->cc)) {
                $mail->setCc($this->cc);
            }
            $mail->setSubject(htmlspecialchars_decode($subject));
            $text = $this->embedImageReplacement($text, $mail);
            $mail->addPart($text, 'text/html', 'utf-8');
            /** @var \TYPO3\Flow\Resource\Resource $resource */
            foreach ($attachedResources as $resource) {
                if ($resource->getStream() !== false) {
                    /* Resource createTemporaryLocalCopy() does not work as the file needs to be stored until flushing
                    the queue. Create a Swift Attachment to let Swiftmailer take care of it. */
                    $content = fread($resource->getStream(), $resource->getFileSize());
                    if ($content !== false) {
                        $swiftAttachment = \Swift_Attachment::newInstance($content, $resource->getFilename(),
                            $resource->getMediaType());
                        $mail->attach($swiftAttachment);
                    }
                }
            }

            $acceptedRecipients = $this->mailer->send($mail);
            if ($acceptedRecipients <= 0) {
                throw new \Exception();
            }
        }
    }

    /**
     * @param $html
     * @param \Swift_Message $mail
     *
     * @return string $html
     */
    private function embedImageReplacement($html, &$mail)
    {
        $callback = function ($matches) use ($mail) {
            return $this->imageReplaceCallback($matches, $mail);
        };
        return preg_replace_callback('/###IMAGE:(.+?)###/', $callback, $html);
    }

    /**
     * @param array $matches
     * @param \Swift_Message $mail $mail
     *
     * @return string
     */
    private function imageReplaceCallback($matches, &$mail)
    {
        if (isset($matches[1])) {
            $asset = $this->assetRepository->findByFilename($matches[1]);
            if ($asset instanceof Asset && $asset->getResource() instanceof Resource) {
                try {
                    $imageSource = $this->resourceManager->getPublicPersistentResourceUri($asset->getResource());
                    if (! empty($imageSource)) {
                        // Attach the message with a "cid"
                        $cid = $mail->embed(\Swift_Image::fromPath($imageSource));
                        return '<img src="' . $cid . '" alt="' . $asset->getTitle() . '" />';
                    }
                } catch (Exception $e) {
                    return '';
                }
            }
        }
        return '';
    }
}
