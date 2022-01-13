<?php

namespace Swisscom\CommunicationDispatcher\Tests\Functional;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Tests\FunctionalTestCase;
use Neos\FluidAdaptor\View\StandaloneView;
use PHPUnit\Framework\MockObject\MockObject;
use Swisscom\CommunicationDispatcher\Dispatcher\Dispatcher;
use Swisscom\CommunicationDispatcher\Service\MessageService;

class MessageServiceTest extends FunctionalTestCase
{

    /**
     * @var Dispatcher|MockObject
     */
    protected $messageService;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $configurationManager = $this->objectManager->get(ConfigurationManager::class);
        $settings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Swisscom.CommunicationDispatcher');
        $this->messageService = $this->getAccessibleMock(MessageService::class, array('dummy'));
        $view = $this->objectManager->get(StandaloneView::class);
        $this->inject($this->messageService, 'settings', $settings);
        $this->inject($this->messageService, 'view', $view);
    }

    /**
     * @test
     */
    public function dispatcherRendersText()
    {
        $params['event']['title'] = 'Foo';
        $renderedText = $this->messageService->_call('render', '{event.title} bar', $params);

        $this->assertSame('Foo bar', $renderedText);
    }
}
