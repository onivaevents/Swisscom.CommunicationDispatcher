<?php

namespace Swisscom\CommunicationDispatcher\Dispatcher;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Channel\ChannelInterface;
use Swisscom\CommunicationDispatcher\Domain\Model\Dto\Recipient;
use TYPO3\Flow\Annotations as Flow;

class Dispatcher implements DispatcherInterface
{

    /**
     * @Flow\InjectConfiguration
     * @var array
     */
    protected $settings;

    /**
     * @var ChannelInterface
     */
    protected $channelInterface;

    /**
     * @var \TYPO3\Fluid\View\StandaloneView
     * @Flow\Inject
     */
    protected $view;

    /**
     * @param ChannelInterface $channelInterface
     * @return void
     */
    public function setChannelInterface(ChannelInterface $channelInterface = null)
    {
        $this->channelInterface = $channelInterface;
    }

    /**
     * @param Recipient $recipient
     * @param string $subject
     * @param string $text
     * @param array $params
     * @param array $attachedResources
     * @return void
     */
    public function dispatch(Recipient $recipient, $subject, $text, $params = array(), $attachedResources = array())
    {
        try {
            $renderedSubject = $this->render($subject, $params);
        } catch (\Exception $exception) {
            $renderedSubject = $this->settings['renderingErrorMessage'];
        }
        try {
            $renderedText = $this->render($text, $params);
        } catch (\Exception $exception) {
            $renderedText = $this->settings['renderingErrorMessage'];
        }
        $this->channelInterface->send($recipient, $renderedSubject, $renderedText, $attachedResources);
    }

    /**
     * @param string $templateSource
     * @param array $params
     * @return string
     */
    protected function render($templateSource, $params = array())
    {
        foreach ($this->settings['templateSourceNamespaces'] as $namespaceKey => $namespaceValue) {
            $templateSource = '{namespace ' . $namespaceKey . '='  . $namespaceValue . '} ' . $templateSource;
        }
        $this->view->setPartialRootPath($this->settings['partialRootPath']);
        $this->view->setTemplateSource($templateSource);
        $this->view->assign('settings', $this->settings);

        foreach ($params as $key => $value) {
            $this->view->assign($key, $value);
        }

        return $this->view->render();
    }
}
