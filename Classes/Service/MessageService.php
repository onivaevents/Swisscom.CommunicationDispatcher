<?php

namespace Swisscom\CommunicationDispatcher\Service;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Exception;
use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\View\StandaloneView;

/**
 * @Flow\Scope("singleton")
 */
class MessageService
{

    /**
     * @Flow\InjectConfiguration
     * @var array
     */
    protected $settings;

    /**
     * @var StandaloneView
     * @Flow\Inject
     */
    protected $view;

    /**
     * @param string $subject
     * @param array $params
     * @return string
     */
    public function renderSubject(string $subject, array $params = []): string
    {
        if (!empty($this->settings['subjectPrefix'])) {
            $subject = $this->settings['subjectPrefix'] . ' ' . $subject;
        }
        return $this->render($subject, $params);
    }

    /**
     * @param string $templateSource
     * @param array $params
     * @return string
     */
    protected function render(string $templateSource, array $params): string
    {
        try {
            foreach ($this->settings['templateSourceNamespaces'] as $namespaceKey => $namespaceValue) {
                $templateSource = '{namespace ' . $namespaceKey . '=' . $namespaceValue . '}' . $templateSource;
            }
            $this->view->setPartialRootPath($this->settings['partialRootPath']);
            $this->view->setTemplateSource($templateSource);

            $params = array_merge_recursive($this->settings['templateViewStaticParameters'], $params);
            foreach ($params as $key => $value) {
                $this->view->assign($key, $value);
            }

            $result = $this->view->render();
        } catch (Exception $exception) {
            $result = $this->settings['renderingErrorMessage'];
        }

        return is_string($result) ? $result : '';
    }

    /**
     * @param string $text
     * @param array $params
     * @return string
     */
    public function renderText(string $text, array $params = []): string
    {
        return $this->render($text, $params);
    }
}
