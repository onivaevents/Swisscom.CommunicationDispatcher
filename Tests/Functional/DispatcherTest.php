<?php

namespace Swisscom\CommunicationDispatcher\Tests\Functional;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Dispatcher\Dispatcher;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Tests\FunctionalTestCase;
use Neos\FluidAdaptor\View\StandaloneView;

/**
 * Class DispatcherTest
 * @package Swisscom\CommunicationDispatcher\Tests\Functional
 */
class DispatcherTest extends FunctionalTestCase
{

    /**
     * @var Dispatcher|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $configurationManager = $this->objectManager->get('Neos\Flow\Configuration\ConfigurationManager');
        $settings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Swisscom.CommunicationDispatcher');
        $this->dispatcher = $this->getAccessibleMock(Dispatcher::class, array('dummy'));
        $view = $this->objectManager->get(StandaloneView::class);
        $this->inject($this->dispatcher, 'settings', $settings);
        $this->inject($this->dispatcher, 'view', $view);
    }

    /**
     * @test
     */
    public function dispatcherRendersText()
    {
        $params['event']['title'] = 'Foo';
        $renderedText = $this->dispatcher->_call('render', '{event.title} bar', $params);

        $this->assertSame('Foo bar', $renderedText);
    }
}
